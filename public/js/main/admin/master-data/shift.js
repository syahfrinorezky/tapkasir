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
    isLoading: false,
    isUpdatingCashierShift: false,
    isAddingShift: false,
    isEditingShift: false,
    isDeletingShift: false,
    validationErrors: {
      name: "",
      start_time: "",
      end_time: "",
      status: "",
    },
    openEditModal: false,
    openAddShiftModal: false,
    openEditShiftModal: false,
    openDeleteShiftModal: false,
    dataCashierPage: 1,
    dataCashierPageSize: 10,
    dataShiftsPage: 1,
    dataShiftsPageSize: 5,

    showTrashCashiers: false,
    trashCashiers: [],
    selectedTrashCashiers: [],
    trashCashiersPage: 1,
    trashCashiersPageSize: 10,

    showTrashShifts: false,
    trashShifts: [],
    selectedTrashShifts: [],
    trashShiftsPage: 1,
    trashShiftsPageSize: 10,

    openRestoreModal: false,
    openDeletePermanentModal: false,
    trashType: 'cashier',
    restoreMode: 'single',
    deletePermanentMode: 'single',
    selectedTrashItem: null,
    isRestoring: false,
    isDeletingPermanent: false,
    isRestoringShift: false,
    isDeletingPermanentShift: false,

    isRestoringCategory: false,
    isDeletingPermanentCategory: false,
    isRestoringLocation: false,
    isDeletingPermanentLocation: false,
    isRestoringRole: false,
    isDeletingPermanentRole: false,

    init() {
      this.fetchData();
    },

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

    get paginatedTrashCashiers() {
      const start = (this.trashCashiersPage - 1) * this.trashCashiersPageSize;
      const end = start + this.trashCashiersPageSize;
      return this.trashCashiers.slice(start, end);
    },
    get totalTrashCashiersPages() {
      return Math.ceil(this.trashCashiers.length / this.trashCashiersPageSize) || 1;
    },
    get paginatedTrashShifts() {
      const start = (this.trashShiftsPage - 1) * this.trashShiftsPageSize;
      const end = start + this.trashShiftsPageSize;
      return this.trashShifts.slice(start, end);
    },
    get totalTrashShiftsPages() {
      return Math.ceil(this.trashShifts.length / this.trashShiftsPageSize) || 1;
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

    changeTrashCashiersPage(page) {
      if (page >= 1 && page <= this.totalTrashCashiersPages)
        this.trashCashiersPage = page;
    },
    changeTrashShiftsPage(page) {
      if (page >= 1 && page <= this.totalTrashShiftsPages)
        this.trashShiftsPage = page;
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

    getTrashCashiersPageNumbers() {
      return Array.from({ length: this.totalTrashCashiersPages }, (_, i) => i + 1);
    },
    getTrashShiftsPageNumbers() {
      return Array.from({ length: this.totalTrashShiftsPages }, (_, i) => i + 1);
    },

    getCashierRowNumber(index) {
      return (this.dataCashierPage - 1) * this.dataCashierPageSize + index + 1;
    },

    getShiftRowNumber(index) {
      return (this.dataShiftsPage - 1) * this.dataShiftsPageSize + index + 1;
    },

    async fetchData() {
      try {
        this.isLoading = true;
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
      } finally {
        this.isLoading = false;
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
      this.clearValidation();
      this.openAddShiftModal = true;
    },

    openEditShift(shift) {
      this.selectedShift = {
        ...shift,
      };
      this.clearValidation();
      this.openEditShiftModal = true;
    },

    openDeleteShift(shift) {
      this.selectedShift = shift;
      this.openDeleteShiftModal = true;
    },

    clearValidation() {
      this.validationErrors = {
        name: "",
        start_time: "",
        end_time: "",
        status: "",
      };
    },

    formatTimeToSeconds(time) {
      if (!time) return time;
      if (/^\d{2}:\d{2}:\d{2}$/.test(time)) return time;
      if (/^\d{2}:\d{2}$/.test(time)) return `${time}:00`;
      return time;
    },

    async updateShift() {
      try {
        this.isUpdatingCashierShift = true;
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

        this.isUpdatingCashierShift = false;

        if (res.ok) {
          this.openEditModal = false;
          this.message = data.message;
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openEditModal = false;
          this.error = data.message || "Gagal memperbarui shift.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isUpdatingCashierShift = false;
        this.error = "Kesalahan koneksi.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async addShift() {
      try {
        this.isAddingShift = true;
        this.selectedShift.start_time = this.formatTimeToSeconds(
          this.selectedShift.start_time
        );
        this.selectedShift.end_time = this.formatTimeToSeconds(
          this.selectedShift.end_time
        );
        const res = await fetch(`/admin/shifts/add`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify(this.selectedShift),
        });

        const data = await res.json();

        this.isAddingShift = false;

        if (res.ok) {
          this.clearValidation();
          this.openAddShiftModal = false;
          this.message = data.message;
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          if (data && data.validation) {
            this.validationErrors = data.validation;
            this.openAddShiftModal = true;
          } else {
            this.openAddShiftModal = false;
            this.error = data.message || "Gagal menambahkan shift.";
            setTimeout(() => (this.error = ""), 3000);
          }
        }
      } catch (error) {
        this.isAddingShift = false;
        this.error = "Terjadi kesalahan saat menambahkan shift.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async editShift() {
      try {
        this.isEditingShift = true;
        this.selectedShift.start_time = this.formatTimeToSeconds(
          this.selectedShift.start_time
        );
        this.selectedShift.end_time = this.formatTimeToSeconds(
          this.selectedShift.end_time
        );
        const res = await fetch(`/admin/shifts/edit/${this.selectedShift.id}`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify(this.selectedShift),
        });

        const data = await res.json();

        this.isEditingShift = false;

        if (res.ok) {
          this.clearValidation();
          this.openEditShiftModal = false;
          this.message = data.message;
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          if (data && data.validation) {
            this.validationErrors = data.validation;
            this.openEditShiftModal = true;
          } else {
            this.openEditShiftModal = false;
            this.error = data.message || "Gagal memperbarui shift.";
            setTimeout(() => (this.error = ""), 3000);
          }
        }
      } catch (error) {
        this.isEditingShift = false;
        this.error = "Terjadi kesalahan saat memperbarui shift.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async deleteShift(id) {
      try {
        this.isDeletingShift = true;
        const res = await fetch(`/admin/shifts/deleteShift/${id}`, {
          method: "DELETE",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        });
        const data = await res.json();

        this.isDeletingShift = false;

        if (res.ok) {
          this.openDeleteShiftModal = false;
          this.message = data.message;
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openDeleteShiftModal = false;
          this.error = data.message || "Gagal menghapus shift.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.isDeletingShift = false;
        this.error = "Terjadi kesalahan saat menghapus shift.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    formatDateTime(dt) {
      if (!dt) return "-";
      try {
        const d = new Date(String(dt).replace(" ", "T"));
        return d.toLocaleString("id-ID");
      } catch {
        return dt;
      }
    },

    toggleTrashCashiers() {
      this.showTrashCashiers = !this.showTrashCashiers;
      if (this.showTrashCashiers) this.fetchTrashCashiers();
    },
    async fetchTrashCashiers() {
      try {
        const response = await fetch('/admin/shifts/cashiers/trash/data');
        const data = await response.json();
        this.trashCashiers = data.cashiers || [];
      } catch (error) {
        console.error('Error fetching trash data:', error);
      }
    },
    toggleTrashShifts() {
      this.showTrashShifts = !this.showTrashShifts;
      if (this.showTrashShifts) this.fetchTrashShifts();
    },
    async fetchTrashShifts() {
      try {
        const response = await fetch('/admin/shifts/trash/data');
        const data = await response.json();
        this.trashShifts = data.shifts || [];
      } catch (error) {
        console.error('Error fetching shift trash data:', error);
      }
    },

    toggleSelectAllTrashCashiers(e) {
      if (e.target.checked) {
        this.selectedTrashCashiers = this.trashCashiers.map(c => c.id);
      } else {
        this.selectedTrashCashiers = [];
      }
    },
    restoreSelectedCashiers() {
      this.trashType = 'cashier';
      this.restoreMode = 'multiple';
      this.openRestoreModal = true;
    },
    deletePermanentSelectedCashiers() {
      this.trashType = 'cashier';
      this.deletePermanentMode = 'multiple';
      this.openDeletePermanentModal = true;
    },

    toggleSelectAllTrashShifts(e) {
      if (e.target.checked) {
        this.selectedTrashShifts = this.trashShifts.map(s => s.id);
      } else {
        this.selectedTrashShifts = [];
      }
    },
    restoreSelectedShifts() {
      this.trashType = 'shift';
      this.restoreMode = 'multiple';
      this.openRestoreModal = true;
    },
    deletePermanentSelectedShifts() {
      this.trashType = 'shift';
      this.deletePermanentMode = 'multiple';
      this.openDeletePermanentModal = true;
    },

    getTrashLabel() {
      return this.trashType === 'cashier' ? 'Kasir' : 'Shift';
    },
    getTrashItemName() {
      if (this.restoreMode === 'multiple' || this.deletePermanentMode === 'multiple') {
        return `${this.getTrashSelectedCount()} item dipilih`;
      }
      if (this.trashType === 'cashier') return this.selectedTrashItem?.nama_lengkap;
      return this.selectedTrashItem?.name;
    },
    getTrashSelectedCount() {
      return this.trashType === 'cashier' ? this.selectedTrashCashiers.length : this.selectedTrashShifts.length;
    },

    confirmRestoreCashier(id) {
      this.trashType = 'cashier';
      this.selectedTrashItem = this.trashCashiers.find(c => c.id === id);
      this.restoreMode = 'single';
      this.openRestoreModal = true;
    },
    confirmDeletePermanentCashier(id) {
      this.trashType = 'cashier';
      this.selectedTrashItem = this.trashCashiers.find(c => c.id === id);
      this.deletePermanentMode = 'single';
      this.openDeletePermanentModal = true;
    },
    confirmRestoreShift(id) {
      this.trashType = 'shift';
      this.selectedTrashItem = this.trashShifts.find(s => s.id === id);
      this.restoreMode = 'single';
      this.openRestoreModal = true;
    },
    confirmDeletePermanentShift(id) {
      this.trashType = 'shift';
      this.selectedTrashItem = this.trashShifts.find(s => s.id === id);
      this.deletePermanentMode = 'single';
      this.openDeletePermanentModal = true;
    },

    async processRestore() {
      if (this.trashType === 'shift') return this.processRestoreShift();

      this.isRestoring = true;
      try {
        let url = '/admin/shifts/cashiers/restore';
        let body = {};

        if (this.restoreMode === 'single') {
          url += `/${this.selectedTrashItem.id}`;
        } else {
          body = { ids: this.selectedTrashCashiers };
        }

        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: this.restoreMode === 'multiple' ? JSON.stringify(body) : null
        });

        if (response.ok) {
          await this.fetchTrashCashiers();
          await this.fetchData();
          this.selectedTrashCashiers = [];
          this.openRestoreModal = false;
          this.message = "Kasir berhasil dipulihkan";
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openRestoreModal = false;
          const data = await response.json().catch(() => ({}));
          this.error = data.message || "Gagal memulihkan kasir.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error('Error restoring data:', error);
      } finally {
        this.isRestoring = false;
      }
    },
    async processDeletePermanent() {
      if (this.trashType === 'shift') return this.processDeletePermanentShift();

      this.isDeletingPermanent = true;
      try {
        let url = '/admin/shifts/cashiers/deletePermanent';
        let body = {};

        if (this.deletePermanentMode === 'single') {
          url += `/${this.selectedTrashItem.id}`;
        } else {
          body = { ids: this.selectedTrashCashiers };
        }

        const response = await fetch(url, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: this.deletePermanentMode === 'multiple' ? JSON.stringify(body) : null
        });

        if (response.ok) {
          await this.fetchTrashCashiers();
          this.selectedTrashCashiers = [];
          this.openDeletePermanentModal = false;
          this.message = "Kasir berhasil dihapus permanen";
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openDeletePermanentModal = false;
          const data = await response.json().catch(() => ({}));
          this.error = data.message || "Gagal menghapus kasir permanen.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error('Error deleting data:', error);
      } finally {
        this.isDeletingPermanent = false;
      }
    },

    async processRestoreShift() {
      this.isRestoringShift = true;
      try {
        let url = '/admin/shifts/restore';
        let body = {};

        if (this.restoreMode === 'single') {
          url += `/${this.selectedTrashItem.id}`;
        } else {
          body = { ids: this.selectedTrashShifts };
        }

        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: this.restoreMode === 'multiple' ? JSON.stringify(body) : null
        });

        if (response.ok) {
          await this.fetchTrashShifts();
          await this.fetchData();
          this.selectedTrashShifts = [];
          this.openRestoreModal = false;
          this.message = "Shift berhasil dipulihkan";
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openRestoreModal = false;
          const data = await response.json().catch(() => ({}));
          this.error = data.message || "Gagal memulihkan shift.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error('Error restoring shift:', error);
      } finally {
        this.isRestoringShift = false;
      }
    },
    async processDeletePermanentShift() {
      this.isDeletingPermanentShift = true;
      try {
        let url = '/admin/shifts/deletePermanent';
        let body = {};

        if (this.deletePermanentMode === 'single') {
          url += `/${this.selectedTrashItem.id}`;
        } else {
          body = { ids: this.selectedTrashShifts };
        }

        const response = await fetch(url, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: this.deletePermanentMode === 'multiple' ? JSON.stringify(body) : null
        });

        if (response.ok) {
          await this.fetchTrashShifts();
          this.selectedTrashShifts = [];
          this.openDeletePermanentModal = false;
          this.message = "Shift berhasil dihapus permanen";
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openDeletePermanentModal = false;
          const data = await response.json().catch(() => ({}));
          this.error = data.message || "Gagal menghapus shift permanen.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error('Error deleting shift:', error);
      } finally {
        this.isDeletingPermanentShift = false;
      }
    },
  };
}
