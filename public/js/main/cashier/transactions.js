function cashierTransactions(baseUrl = "") {
  return {
    baseUrl: baseUrl || "",
    barcode: "",
    cart: [],
    payment: 0,
    paymentMethod: "cash",
    showReceiptModal: false,
    message: "",
    error: "",
    suggestions: [],
    showSuggestions: false,
    _debounce: null,
    _sugCtrl: null,
    isLoading: false,
    highlightedIndex: -1,
    cartPage: 1,
    cartPageSize: 10,
    showShiftWarning: false,
    shiftCountdownSec: 0,
    _shiftPollTimer: null,
    _shiftCountdownTimer: null,
    pendingTransaction: null,

    init() {
      setTimeout(() => {
        if (this.$refs.barcodeInput) this.$refs.barcodeInput.focus();
      }, 300);
      this.startShiftWatcher();
      this.checkPendingTransaction();
    },

    async scanBarcode() {
      const code = (this.barcode || "").trim();
      if (!code) return;

      try {
        const res = await fetch(
          `/cashier/transactions/product/${encodeURIComponent(code)}`,
          {
            method: "GET",
            credentials: "same-origin",
            headers: {
              Accept: "application/json",
              "X-Requested-With": "XMLHttpRequest",
            },
          }
        );

        if (!res.ok) {
          const err = await res.json().catch(() => ({}));
          this.error = err.message || "Produk tidak ditemukan";
          setTimeout(() => (this.error = ""), 3000);
          this.barcode = "";
          if (this.$refs.barcodeInput) this.$refs.barcodeInput.focus();
          return;
        }

        const json = await res.json();
        const p = json.product || {};
        const stock = Number(p.stock ?? 0);
        if (!Number.isNaN(stock) && stock <= 0) {
          this.error = "Stok produk kosong";
          setTimeout(() => (this.error = ""), 3000);
        } else {
          this.addToCart({
            product_id: p.id,
            product_name: p.product_name,
            price: Number(p.price || 0),
            stock: stock,
          });
        }
        this.barcode = "";
        this.suggestions = [];
        this.showSuggestions = false;
        if (this.$refs.barcodeInput) this.$refs.barcodeInput.focus();
      } catch (e) {
        console.error(e);
        this.error = "Gagal memuat produk";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    onInput() {
      clearTimeout(this._debounce);
      this._debounce = setTimeout(() => this.fetchSuggestions(), 250);
    },

    async fetchSuggestions() {
      const q = (this.barcode || "").trim();
      if (!q) {
        this.suggestions = [];
        this.showSuggestions = false;
        this.highlightedIndex = -1;
        return;
      }

      try {
        if (this._sugCtrl) {
          try {
            this._sugCtrl.abort();
          } catch (e) { }
        }
        this._sugCtrl = new AbortController();

        const res = await fetch(
          `/cashier/transactions/product?q=${encodeURIComponent(q)}`,
          {
            method: "GET",
            credentials: "same-origin",
            headers: {
              Accept: "application/json",
              "X-Requested-With": "XMLHttpRequest",
            },
            signal: this._sugCtrl.signal,
          }
        );
        if (!res.ok) {
          this.suggestions = [];
          this.showSuggestions = false;
          return;
        }
        let json = null;
        try {
          json = await res.json();
        } catch (e) {
          console.error("Suggestion JSON parse failed", e);
          json = null;
        }
        this.suggestions =
          json && Array.isArray(json.products) ? json.products : [];
        this.showSuggestions = this.suggestions.length > 0;
        this.highlightedIndex = this.showSuggestions ? 0 : -1;
      } catch (e) {
        if (e?.name === "AbortError") {
          return;
        }
        console.error("Suggestion fetch failed", e);
        this.suggestions = [];
        this.showSuggestions = false;
        this.highlightedIndex = -1;
      }
    },

    async selectSuggestion(p) {
      try {
        const barcode = p.barcode || this.barcode;
        if (!barcode) throw new Error("Barcode tidak tersedia");
        const res = await fetch(
          `/cashier/transactions/product/${encodeURIComponent(barcode)}`,
          {
            method: "GET",
            credentials: "same-origin",
            headers: {
              Accept: "application/json",
              "X-Requested-With": "XMLHttpRequest",
            },
          }
        );
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
          this.error = (data && data.message) || "Produk tidak ditemukan";
          setTimeout(() => (this.error = ""), 3000);
          return;
        }
        const pr = data.product || {};
        const stock = Number(pr.stock ?? 0);
        if (!Number.isNaN(stock) && stock <= 0) {
          this.error = "Stok produk kosong";
          setTimeout(() => (this.error = ""), 3000);
          return;
        }
        this.addToCart({
          product_id: pr.id,
          product_name: pr.product_name,
          price: Number(pr.price || 0),
          stock: stock,
        });
      } finally {
        this.suggestions = [];
        this.showSuggestions = false;
        this.barcode = "";
        if (this.$refs.barcodeInput) this.$refs.barcodeInput.focus();
      }
    },

    addToCart(product) {
      if (product && typeof product.stock !== "undefined") {
        const st = Number(product.stock);
        if (!Number.isNaN(st) && st <= 0) {
          this.error = "Stok produk kosong";
          setTimeout(() => (this.error = ""), 3000);
          return;
        }
      }
      const idx = this.cart.findIndex(
        (c) => c.product_id == product.product_id
      );
      if (idx !== -1) {
        const current = this.cart[idx];
        const st = Number(
          typeof current.stock !== "undefined" ? current.stock : product.stock
        );
        if (!Number.isNaN(st)) {
          if (current.quantity + 1 > st) {
            this.error = "Stok produk tidak mencukupi";
            setTimeout(() => (this.error = ""), 3000);
            return;
          }
          current.quantity += 1;
        } else {
          this.cart[idx].quantity += 1;
        }
      } else {
        this.cart.push({ ...product, quantity: 1 });
        try {
          this.cartPage = this.totalCartPages;
        } catch (e) { }
      }
    },

    moveHighlight(delta) {
      if (!this.showSuggestions || this.suggestions.length === 0) return;
      const len = this.suggestions.length;
      let idx = this.highlightedIndex;
      if (idx === -1) idx = 0;
      idx = (idx + delta + len) % len;
      this.highlightedIndex = idx;
      this.$nextTick(() => {
        const el = document.getElementById("sug-" + idx);
        if (el && el.scrollIntoView) {
          el.scrollIntoView({ block: "nearest" });
        }
      });
    },

    async selectHighlighted() {
      if (!this.showSuggestions) return;
      const idx = this.highlightedIndex;
      if (idx < 0 || idx >= this.suggestions.length) return;
      const p = this.suggestions[idx];
      await this.selectSuggestion(p);
    },

    closeSuggestions() {
      this.showSuggestions = false;
      this.suggestions = [];
      this.highlightedIndex = -1;
    },

    getProductImage(p) {
      const filename =
        (p && (p.photo || p.image || p.photo_filename || p.image_filename)) ||
        "";

      const placeholder =
        "data:image/svg+xml;utf8," +
        encodeURIComponent(
          '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"><rect width="100%" height="100%" fill="#f0f0f0"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="10" fill="#999">IMG</text></svg>'
        );

      if (!filename) return placeholder;

      if (/^https?:\/\//i.test(filename)) return filename;

      const normBase = (this.baseUrl || "").replace(/\/+$/, "");

      if (filename.startsWith("/")) {
        return normBase ? `${normBase}${filename}` : filename;
      }

      if (filename.includes("/")) {
        return `${normBase}/${filename.replace(/^\/+/, "")}`;
      }

      return `${normBase || ""}/uploads/products/${filename}`.replace(
        /\/+uploads/,
        "/uploads"
      );
    },

    updateQty(idx) {
      if (this.cart[idx].quantity <= 0) this.cart[idx].quantity = 1;
      setTimeout(() => {
        try {
          this.focusBarcode();
        } catch (e) { }
      }, 50);
    },

    removeItem(idx) {
      this.cart.splice(idx, 1);
      try {
        if (this.cartPage > this.totalCartPages)
          this.cartPage = this.totalCartPages;
      } catch (e) { }
      setTimeout(() => {
        try {
          this.focusBarcode();
        } catch (e) { }
      }, 50);
    },

    get total() {
      return this.cart.reduce((s, it) => s + it.price * it.quantity, 0);
    },

    get change() {
      return Math.max(0, (this.payment || 0) - this.total);
    },

    formatCurrency(val) {
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
      }).format(val);
    },

    async submitTransaction() {
      if (this.cart.length === 0) {
        this.error = "Keranjang kosong";
        setTimeout(() => (this.error = ""), 3000);
        return;
      }

      if (this.paymentMethod === "cash" && this.payment < this.total) {
        this.error = "Pembayaran kurang dari total";
        setTimeout(() => (this.error = ""), 3000);
        return;
      }

      this.isLoading = true;
      const items = this.cart.map((it) => ({
        product_id: it.product_id,
        quantity: it.quantity,
      }));

      try {
        const res = await fetch("/cashier/transactions/create", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            items,
            payment: this.paymentMethod === "cash" ? this.payment : this.total,
            payment_method: this.paymentMethod,
          }),
        });

        const json = await res.json();

        if (!res.ok) {
          throw new Error(json.message || "Gagal menyimpan transaksi");
        }

        if (this.paymentMethod === "qris" && json.snap_token) {
          this.isLoading = false;
          if (typeof window.snap === "undefined") {
            throw new Error("Midtrans Snap JS not loaded");
          }
          window.snap.pay(json.snap_token, {
            onSuccess: async (result) => {
              await this.handlePaymentSuccess(json.transaction_id, result);
            },
            onPending: (result) => {
              this.message = "Menunggu pembayaran...";
            },
            onError: (result) => {
              this.error = "Pembayaran Gagal!";
              setTimeout(() => (this.error = ""), 3000);
            },
            onClose: () => {
              this.message = "Pembayaran dapat dilanjutkan nanti.";
              setTimeout(() => (this.message = ""), 3000);
              this.checkPendingTransaction();
            },
          });
        } else {
          this.isLoading = false;
          this.message = "Transaksi berhasil";
          this.cart = [];
          this.payment = 0;

          setTimeout(() => (this.message = ""), 3000);
          if (this.$refs.barcodeInput) this.$refs.barcodeInput.focus();

          if (json.transaction_id) {
            await this.showReceipt(json.transaction_id);
          }
        }
      } catch (error) {
        this.isLoading = false;
        console.error("Transaction error:", error);
        this.error = error.message;
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async checkPendingTransaction() {
      try {
        const res = await fetch('/cashier/transactions/check-pending', {
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        if (res.ok) {
          const data = await res.json();
          if (data.has_pending) {
            this.pendingTransaction = data;
          }
        }
      } catch (e) {
        console.error('Failed to check pending transaction', e);
      }
    },

    async resumePendingPayment() {
      if (!this.pendingTransaction || !this.pendingTransaction.snap_token) {
        return;
      }

      const pending = this.pendingTransaction;

      if (typeof window.snap === 'undefined') {
        this.error = 'Midtrans Snap JS not loaded';
        setTimeout(() => (this.error = ''), 3000);
        return;
      }

      window.snap.pay(pending.snap_token, {
        onSuccess: async (result) => {
          await this.handlePaymentSuccess(pending.transaction_id, result);
        },
        onPending: (result) => {
          this.message = 'Menunggu pembayaran...';
          setTimeout(() => (this.message = ''), 3000);
        },
        onError: (result) => {
          this.error = 'Pembayaran Gagal!';
          setTimeout(() => (this.error = ''), 3000);
        },
        onClose: () => {
          // Jangan auto-cancel, biarkan pending dan munculkan kembali banner
          this.message = 'Anda dapat melanjutkan pembayaran nanti';
          setTimeout(() => (this.message = ''), 3000);
          this.checkPendingTransaction();
        },
      });

      this.pendingTransaction = null;
    },

    async cancelPendingTransaction() {
      if (!this.pendingTransaction) {
        return;
      }

      try {
        const res = await fetch('/cashier/transactions/cancel', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({
            transaction_id: this.pendingTransaction.transaction_id,
          }),
        });

        const data = await res.json();
        this.pendingTransaction = null;
        this.message = data.message || 'Transaksi dibatalkan';
        setTimeout(() => (this.message = ''), 3000);
      } catch (e) {
        console.error('Cancel failed', e);
        this.error = 'Gagal membatalkan transaksi';
        setTimeout(() => (this.error = ''), 3000);
      }
    },

    async handlePaymentSuccess(transactionId, result) {
      try {
        await fetch('/cashier/transactions/finish', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({
            transaction_id: transactionId,
            midtrans_id: result.transaction_id,
            payment_type: result.payment_type,
          }),
        });
      } catch (e) {
        console.error('Failed to update transaction status', e);
      }

      this.message = 'Pembayaran Berhasil!';
      this.cart = [];
      this.payment = 0;
      setTimeout(() => (this.message = ''), 3000);

      if (transactionId) {
        this.showReceipt(transactionId);
      }
    },

    async showReceipt(transactionId) {
      const receiptUrl = `/cashier/transactions/receipt/${transactionId}`;

      try {
        const fetchRes = await fetch(receiptUrl, {
          credentials: "same-origin",
          headers: { Accept: "text/html" },
        });

        const html = await fetchRes.text().catch(() => null);

        if (!fetchRes.ok) {
          const snippet = html ? " - " + html.toString().slice(0, 200) : "";
          throw new Error(
            "Gagal memuat struk: " +
            fetchRes.status +
            " " +
            fetchRes.statusText +
            snippet
          );
        }

        const iframe = document.createElement("iframe");
        iframe.style.position = "fixed";
        iframe.style.width = "0";
        iframe.style.height = "0";
        iframe.style.border = "0";
        iframe.style.left = "-9999px";
        iframe.style.top = "-9999px";
        iframe.setAttribute("aria-hidden", "true");

        const loadPromise = new Promise((resolve, reject) => {
          const timeoutId = setTimeout(
            () => reject(new Error("Timeout loading receipt iframe")),
            10000
          );

          iframe.onload = () => {
            clearTimeout(timeoutId);
            resolve();
          };

          iframe.onerror = (err) => {
            clearTimeout(timeoutId);
            reject(err || new Error("Error loading hidden receipt iframe"));
          };
        });

        document.body.appendChild(iframe);

        iframe.srcdoc = html || "<p>Gagal memuat struk</p>";

        await loadPromise;

        try {
          const w = iframe.contentWindow;
          if (w) {
            w.focus();
            setTimeout(() => {
              try {
                w.print();
              } catch (printErr) {
                console.warn("Print failed:", printErr);
              }
            }, 200);
          }
        } catch (e) {
          console.warn("Printing error:", e);
        }

        setTimeout(() => {
          try {
            document.body.removeChild(iframe);
          } catch (e) { }
        }, 2000);

        setTimeout(() => {
          try {
            if (this.$refs && this.$refs.barcodeInput) {
              this.$refs.barcodeInput.focus();
              this.barcode = "";
            } else {
              const inp = document.querySelector('input[x-ref="barcodeInput"]');
              if (inp) inp.focus();
            }
          } catch (e) { }
        }, 300);
      } catch (error) {
        console.error("Failed to show receipt:", error);
        this.showError(error.message || "Gagal menampilkan struk");
        try {
          if (this.$refs && this.$refs.barcodeInput)
            this.$refs.barcodeInput.focus();
        } catch (e) { }
      }
    },

    showError(message) {
      this.error = message;
      setTimeout(() => {
        this.error = "";
      }, 3000);
    },

    focusBarcode() {
      try {
        if (this.$refs && this.$refs.barcodeInput) {
          this.$refs.barcodeInput.focus();
          return;
        }

        const inp = document.querySelector('input[x-ref="barcodeInput"]');
        if (inp) inp.focus();
      } catch (e) { }
    },

    startShiftWatcher() {
      try {
        if (this._shiftPollTimer) clearInterval(this._shiftPollTimer);
      } catch (e) { }
      this.pollShiftStatus();
      this._shiftPollTimer = setInterval(() => this.pollShiftStatus(), 30000);
    },

    async pollShiftStatus() {
      try {
        const res = await fetch("/cashier/shift/status", {
          credentials: "same-origin",
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
        });

        if (!res.ok) {
          if (res.status === 401 || res.status === 403) {
            window.location.href = "/";
            return;
          }
          return;
        }
        const data = await res.json().catch(() => ({}));
        if (!data || !data.active) {
          this.stopShiftCountdown();
          this.showShiftWarning = false;
          return;
        }

        const secs = Number(data.seconds_left || 0);
        if (secs <= 0) {
          this.stopShiftCountdown();
          window.location.href = "/logout";
          return;
        }
        if (secs <= 60) {
          this.startShiftCountdown(secs);
        } else {
          this.stopShiftCountdown();
          this.showShiftWarning = false;
        }
      } catch (e) {
        console.warn("Shift status check failed", e);
      }
    },

    startShiftCountdown(seconds) {
      this.shiftCountdownSec = Math.max(0, Math.floor(seconds));
      this.showShiftWarning = true;
      try {
        if (this._shiftCountdownTimer) clearInterval(this._shiftCountdownTimer);
      } catch (e) { }
      this._shiftCountdownTimer = setInterval(() => {
        this.shiftCountdownSec = Math.max(0, this.shiftCountdownSec - 1);
        if (this.shiftCountdownSec <= 0) {
          clearInterval(this._shiftCountdownTimer);
          this._shiftCountdownTimer = null;
          window.location.href = "/logout";
        }
      }, 1000);
    },

    stopShiftCountdown() {
      try {
        if (this._shiftCountdownTimer) clearInterval(this._shiftCountdownTimer);
      } catch (e) { }
      this._shiftCountdownTimer = null;
    },

    formatSeconds(s) {
      const sec = Math.max(0, Math.floor(s || 0));
      const mm = String(Math.floor(sec / 60)).padStart(2, "0");
      const ss = String(sec % 60).padStart(2, "0");
      return `${mm}:${ss}`;
    },

    adjustQty(productId, delta) {
      const idx = this.cart.findIndex(
        (c) => String(c.product_id) === String(productId)
      );
      if (idx === -1) return;
      const current = this.cart[idx];
      const st = Number(current.stock);
      let next = (Number(current.quantity) || 1) + (delta || 0);
      if (next < 1) next = 1;
      if (!Number.isNaN(st) && next > st) {
        this.error = "Stok produk tidak mencukupi";
        setTimeout(() => (this.error = ""), 3000);
        return;
      }
      this.cart[idx].quantity = next;
      setTimeout(() => this.focusBarcode(), 50);
    },

    updateQtyByProduct(productId) {
      const idx = this.cart.findIndex(
        (c) => String(c.product_id) === String(productId)
      );
      if (idx === -1) return;
      const current = this.cart[idx];
      const st = Number(current.stock);
      let q = Number(current.quantity) || 1;
      if (q < 1) q = 1;
      if (!Number.isNaN(st) && q > st) {
        this.error = "Stok produk tidak mencukupi";
        setTimeout(() => (this.error = ""), 3000);
        q = st;
      }
      this.cart[idx].quantity = q;
      setTimeout(() => this.focusBarcode(), 50);
    },

    sanitizeQtyInput(productId, $event) {
      try {
        const raw = String(
          ($event && $event.target && $event.target.value) || ""
        );
        const digits = raw.replace(/[^0-9]/g, "");
        if ($event && $event.target) $event.target.value = digits;
        const idx = this.cart.findIndex(
          (c) => String(c.product_id) === String(productId)
        );
        if (idx === -1) return;
        this.cart[idx].quantity = digits === "" ? "" : Number(digits);
      } catch (e) { }
    },

    get filteredCart() {
      return Array.isArray(this.cart) ? this.cart : [];
    },

    get paginatedCart() {
      const start = (this.cartPage - 1) * this.cartPageSize;
      const end = start + this.cartPageSize;
      return this.filteredCart.slice(start, end);
    },

    get totalCartPages() {
      return Math.ceil(this.filteredCart.length / this.cartPageSize) || 1;
    },

    changeCartPage(page) {
      if (page >= 1 && page <= this.totalCartPages) this.cartPage = page;
    },

    getCartPageNumbers() {
      return Array.from({ length: this.totalCartPages }, (_, i) => i + 1);
    },
  };
}
