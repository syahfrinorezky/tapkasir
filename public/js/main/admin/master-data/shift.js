function shiftManagement() {
  return {
    cashiers: [],
    shifts: [],
    selectedCashier: null,
    selectedShift: {
      id: null,
      name: "",
      start_time: "",
      end_time: "",
      status: "active",
    },
    selectedShiftId: null,
    message: "",
    error: "",
    openEditModal: false,
    openAddShiftModal: false,
    openEditShiftModal: false,
    openDeleteShiftModal: false,
    dataCashierPage: 1,
    dataCashierPageSize: 10,
    dataShiftsPage: 1,
    dataShiftsPageSize: 5,

    get paginatedCashiers() {
      const start = (this.dataCashierPage - 1) * this.dataCashierPageSize;
      const end = start + this.dataCashierPageSize;
      return this.cashiers.slice(start, end);
    },

    get totalCashierPages() {
      return Math.ceil(this.cashiers.length / this.dataCashierPageSize);
    },

    get paginatedShifts() {
      const start = (this.dataShiftsPage - 1) * this.dataShiftsPageSize;
      const end = start + this.dataShiftsPageSize;
      return this.shifts.slice(start, end);
    },

    get totalShiftsPages() {
      return Math.ceil(this.shifts.length / this.dataShiftsPageSize);
    },

    changeDataCashierPage(page) {
      if (page >= 1 && page <= this.totalCashierPages) {
        this.dataCashierPage = page;
      }
    },

    changeDataShiftsPage(page) {
      if (page >= 1 && page <= this.totalShiftsPages) {
        this.dataShiftsPage = page;
      }
    },

    getDataCashiersNumber() {
      const pages = [];
      for (let i = 1; i <= this.totalCashierPages; i++) {
        pages.push(i);
      }
      return pages;
    },

    getDataShiftsNumber() {
      const pages = [];
      for (let i = 1; i <= this.totalShiftsPages; i++) {
        pages.push(i);
      }
      return pages;
    },

    getCashierRowNumber(index) {
      return (this.dataCashierPage - 1) * this.dataCashierPageSize + index + 1;
    },

    getShiftRowNumber(index) {
      return (this.dataShiftsPage - 1) * this.dataShiftsPageSize + index + 1;
    },

    async fetchData() {
      try {
        const res = await fetch(`/admin/shifts/data`, {
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        });
        const data = await res.json();
        this.cashiers = data.cashiers || [];
        this.shifts = data.shifts || [];
      } catch (e) {
        console.error(e);
        this.error = "Gagal memuat data shift & kasir.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    openEditCashier(cashier) {
      this.selectedCashier = cashier;
      this.selectedShiftId = cashier.shift_id || "";
      this.openEditModal = true;
    },

    openAddShift() {
      this.selectedShift = {
        id: null,
        name: "",
        start_time: "",
        end_time: "",
        status: "active",
      };
      this.openAddShiftModal = true;
    },

    openEditShift(shift) {
      this.selectedShift = {
        ...shift,
      };
      this.openEditShiftModal = true;
    },

    openDeleteShift(shift) {
      this.selectedShift = shift;
      this.openDeleteShiftModal = true;
    },

    async updateShift() {
      try {
        const res = await fetch(
          `/admin/shifts/updateCashierShift/${this.selectedCashier.id}`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
              "X-Requested-With": "XMLHttpRequest",
            },
            body: `shift_id=${this.selectedShiftId}`,
          }
        );
        const data = await res.json();

        if (res.ok) {
          this.message = data.message;
          this.openEditModal = false;
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openEditModal = false;
          this.error = data.message || "Gagal memperbarui shift.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.error = "Kesalahan koneksi.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async addShift() {
      try {
        const res = await fetch(`/admin/shifts/add`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify(this.selectedShift),
        });

        const data = await res.json();

        if (res.ok) {
          this.message = data.message;
          await this.fetchData();
          this.openAddShiftModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openAddShiftModal = false;
          this.error = data.message || "Gagal menambahkan shift.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.error = "Terjadi kesalahan saat menambahkan shift.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async editShift() {
      try {
        const res = await fetch(`/admin/shifts/edit/${this.selectedShift.id}`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify(this.selectedShift),
        });

        const data = await res.json();

        if (res.ok) {
          this.message = data.message;
          await this.fetchData();
          this.openEditShiftModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openEditShiftModal = false;
          this.error = data.message || "Gagal memperbarui shift.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.error = "Terjadi kesalahan saat memperbarui shift.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async deleteShift(id) {
      try {
        const res = await fetch(`/admin/shifts/deleteShift/${id}`, {
          method: "DELETE",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        });
        const data = await res.json();

        if (res.ok) {
          this.message = data.message;
          await this.fetchData();
          this.openDeleteShiftModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openDeleteShiftModal = false;
          this.error = data.message || "Gagal menghapus shift.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.error = "Terjadi kesalahan saat menghapus shift.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },
  };
}
