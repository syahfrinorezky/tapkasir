function userManagement() {
  return {
    approved: [],
    pending: [],
    roles: [],
    message: "",
    error: "",
    isLoading: false,
    approvingUserId: null,
    rejectingUserId: null,
    isUpdatingInfo: false,
    isDeletingUser: false,
    isAddingRole: false,
    isEditingRole: false,
    isDeletingRole: false,
    selectedUser: {
      id: null,
      nama_lengkap: "",
      email: "",
      role_id: "",
      role_name: "",
      status: "",
    },
    selectedRole: {
      id: null,
      role_name: "",
    },
    openDetailModal: false,
    openEditModal: false,
    openDeleteModal: false,
    openRoleModal: false,
    openRoleEditModal: false,
    openRoleDeleteModal: false,
    dataUserPage: 1,
    dataUserPageSize: 10,
    dataPendingPage: 1,
    dataPendingPageSize: 5,
    dataRolesPage: 1,
    dataRolesPageSize: 5,

    init() {
      this.fetchRoles();
      this.fetchUsers("approved");
      this.fetchUsers("pending");
    },

    get paginatedUsers() {
      const start = (this.dataUserPage - 1) * this.dataUserPageSize;
      const end = start + this.dataUserPageSize;
      return this.approved.slice(start, end);
    },

    get totalUserPages() {
      return Math.ceil(this.approved.length / this.dataUserPageSize);
    },

    get paginatedPending() {
      const start = (this.dataPendingPage - 1) * this.dataPendingPageSize;
      const end = start + this.dataPendingPageSize;
      return this.pending.slice(start, end);
    },

    get totalPendingPages() {
      return Math.ceil(this.pending.length / this.dataPendingPageSize);
    },

    get paginatedRoles() {
      const start = (this.dataRolesPage - 1) * this.dataRolesPageSize;
      const end = start + this.dataRolesPageSize;
      return this.roles.slice(start, end);
    },

    get totalRolesPages() {
      return Math.ceil(this.roles.length / this.dataRolesPageSize);
    },

    changeDataUserPage(page) {
      if (page >= 1 && page <= this.totalUserPages) {
        this.dataUserPage = page;
      }
    },

    changeDataPendingPage(page) {
      if (page >= 1 && page <= this.totalPendingPages) {
        this.dataPendingPage = page;
      }
    },

    changeDataRolesPage(page) {
      if (page >= 1 && page <= this.totalRolesPages) {
        this.dataRolesPage = page;
      }
    },

    getDataUsersNumber() {
      const pages = [];
      for (let i = 1; i <= this.totalUserPages; i++) {
        pages.push(i);
      }
      return pages;
    },

    getDataPendingNumber() {
      const pages = [];
      for (let i = 1; i <= this.totalPendingPages; i++) {
        pages.push(i);
      }
      return pages;
    },

    getDataRolesNumber() {
      const pages = [];
      for (let i = 1; i <= this.totalRolesPages; i++) {
        pages.push(i);
      }
      return pages;
    },

    getUserRowNumber(index) {
      return (this.dataUserPage - 1) * this.dataUserPageSize + index + 1;
    },

    getPendingRowNumber(index) {
      return (this.dataPendingPage - 1) * this.dataPendingPageSize + index + 1;
    },

    getRoleRowNumber(index) {
      return (this.dataRolesPage - 1) * this.dataRolesPageSize + index + 1;
    },

    openDetail(user) {
      this.selectedUser = user;
      this.openDetailModal = true;
    },

    async openEdit(user) {
      await this.fetchRoles();

      this.selectedUser = {
        ...user,
        role_id: user.role_id,
      };
      this.openEditModal = true;
    },

    openDelete(user) {
      this.selectedUser = user;
      this.openDeleteModal = true;
    },

    openRole() {
      this.selectedRole = {
        id: null,
        role_name: "",
      };
      this.openRoleModal = true;
    },

    openRoleEdit(role) {
      this.selectedRole = {
        ...role,
      };
      this.openRoleEditModal = true;
    },

    openRoleDelete(role) {
      this.selectedRole = {
        ...role,
      };
      this.openRoleDeleteModal = true;
    },

    async fetchRoles() {
      try {
        const res = await fetch(`/admin/roles`, {
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        });
        const data = await res.json();
        this.roles = data;

        this.dataRolesPage = 1;
      } catch (e) {
        this.error = "Gagal mengambil data role.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async addRole() {
      if (this.selectedRole.role_name.trim() === "") {
        this.error = "Nama role tidak boleh kosong.";
        setTimeout(() => (this.error = ""), 3000);
        return;
      }

      try {
        this.isAddingRole = true;
        const res = await fetch(`/admin/roles/add`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            role_name: this.selectedRole.role_name,
          }),
        });

        const data = await res.json();

        if (res.ok) {
          this.message = data.message;
          await this.fetchRoles();
          this.openRoleModal = false;

          this.selectedRole = {
            id: null,
            role_name: "",
          };

          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openRoleModal = false;
          this.error = data.message || "Gagal menambahkan role.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.error = "Terjadi kesalahan saat menambahkan role.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isAddingRole = false;
      }
    },

    async editRole() {
      try {
        this.isEditingRole = true;
        const res = await fetch(`/admin/roles/edit/${this.selectedRole.id}`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            role_name: this.selectedRole.role_name,
          }),
        });

        const data = await res.json();

        if (res.ok) {
          this.message = data.message;
          await this.fetchRoles();
          this.openRoleEditModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openRoleEditModal = false;
          this.error = data.message || "Gagal memperbarui role.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.error = "Terjadi kesalahan saat memperbarui role.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isEditingRole = false;
      }
    },

    async fetchUsers(type) {
      try {
        const res = await fetch(`/admin/users?type=${type}`, {
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        });
        const data = await res.json();
        if (type === "approved") {
          this.approved = data;
          this.dataUserPage = 1;
        }
        if (type === "pending") {
          this.pending = data;
          this.dataPendingPage = 1;
        }
      } catch (e) {
        this.error = "Gagal mengambil data pengguna.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async updateStatus(id, status) {
      try {
        if (status === "approved") this.approvingUserId = id;
        if (status === "rejected") this.rejectingUserId = id;
        const res = await fetch(`/admin/users/updateStatus/${id}`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            status,
          }),
        });
        const data = await res.json();

        if (res.ok) {
          this.message = data.message;
          await this.fetchUsers("pending");
          await this.fetchUsers("approved");
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal memperbarui status.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (err) {
        this.error = "Terjadi kesalahan server.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        if (this.approvingUserId === id) this.approvingUserId = null;
        if (this.rejectingUserId === id) this.rejectingUserId = null;
      }
    },

    async updateInfo() {
      try {
        this.isUpdatingInfo = true;
        const res = await fetch(
          `/admin/users/updateInfo/${this.selectedUser.id}`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              nama_lengkap: this.selectedUser.nama_lengkap,
              email: this.selectedUser.email,
              role_id: this.selectedUser.role_id,
            }),
          }
        );
        const data = await res.json();

        if (res.ok) {
          this.message = data.message;
          await this.fetchUsers("approved");
          this.openEditModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openEditModal = false;
          this.error = data.message || "Gagal memperbarui informasi.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.error = "Terjadi kesalahan server.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isUpdatingInfo = false;
      }
    },

    async deleteUser(id) {
      try {
        this.isDeletingUser = true;
        const res = await fetch(`/admin/users/delete/${id}`, {
          method: "DELETE",
        });
        const data = await res.json();
        if (res.ok) {
          this.message = data.message;
          await this.fetchUsers("approved");
          this.openDeleteModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openDeleteModal = false;
          this.error = data.message || "Gagal menghapus user.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.openDeleteModal = false;
        this.error = "Terjadi kesalahan server.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isDeletingUser = false;
      }
    },

    async deleteRole(id) {
      try {
        this.isDeletingRole = true;
        const res = await fetch(`/admin/roles/delete/${id}`, {
          method: "DELETE",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        });
        const data = await res.json();

        if (res.ok) {
          this.message = data.message;
          await this.fetchRoles();
          this.openRoleDeleteModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openRoleDeleteModal = false;
          this.error = data.message || "Gagal menghapus role.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.openRoleDeleteModal = false;
        this.error = "Terjadi kesalahan saat menghapus role.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isDeletingRole = false;
      }
    },
  };
}
