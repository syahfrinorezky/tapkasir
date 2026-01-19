function userManagement(currentUserId = null) {
  return {
    approved: [],
    pending: [],
    roles: [],
    message: "",
    error: "",
    isLoading: false,
    approvingUserId: null,
    rejectingUserId: null,
    currentUserId: currentUserId,
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

    showTrashUsers: false,
    trashUsers: [],
    selectedTrashUsers: [],
    trashUsersPage: 1,
    trashUsersPageSize: 10,

    showTrashRoles: false,
    trashRoles: [],
    selectedTrashRoles: [],
    trashRolesPage: 1,
    trashRolesPageSize: 10,

    openRestoreModal: false,
    openDeletePermanentModal: false,
    trashType: 'user',
    restoreMode: 'single',
    deletePermanentMode: 'single',
    selectedTrashItem: null,
    isRestoring: false,
    isDeletingPermanent: false,
    isRestoringRole: false,
    isDeletingPermanentRole: false,

    isRestoringCategory: false,
    isDeletingPermanentCategory: false,
    isRestoringLocation: false,
    isDeletingPermanentLocation: false,
    isRestoringShift: false,
    isDeletingPermanentShift: false,

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

    get paginatedTrashUsers() {
      const start = (this.trashUsersPage - 1) * this.trashUsersPageSize;
      const end = start + this.trashUsersPageSize;
      return this.trashUsers.slice(start, end);
    },
    get totalTrashUsersPages() {
      return Math.ceil(this.trashUsers.length / this.trashUsersPageSize) || 1;
    },
    get paginatedTrashRoles() {
      const start = (this.trashRolesPage - 1) * this.trashRolesPageSize;
      const end = start + this.trashRolesPageSize;
      return this.trashRoles.slice(start, end);
    },
    get totalTrashRolesPages() {
      return Math.ceil(this.trashRoles.length / this.trashRolesPageSize) || 1;
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

    changeTrashUsersPage(page) {
      if (page >= 1 && page <= this.totalTrashUsersPages)
        this.trashUsersPage = page;
    },
    changeTrashRolesPage(page) {
      if (page >= 1 && page <= this.totalTrashRolesPages)
        this.trashRolesPage = page;
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

    getTrashUsersPageNumbers() {
      return Array.from({ length: this.totalTrashUsersPages }, (_, i) => i + 1);
    },
    getTrashRolesPageNumbers() {
      return Array.from({ length: this.totalTrashRolesPages }, (_, i) => i + 1);
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

        this.isAddingRole = false;

        if (res.ok) {
          this.openRoleModal = false;
          this.message = data.message;
          await this.fetchRoles();

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
        this.isAddingRole = false;
        this.error = "Terjadi kesalahan saat menambahkan role.";
        setTimeout(() => (this.error = ""), 3000);
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

        this.isEditingRole = false;

        if (res.ok) {
          this.openRoleEditModal = false;
          this.message = data.message;
          await this.fetchRoles();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openRoleEditModal = false;
          this.error = data.message || "Gagal memperbarui role.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.isEditingRole = false;
        this.error = "Terjadi kesalahan saat memperbarui role.";
        setTimeout(() => (this.error = ""), 3000);
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
          if (this.currentUserId) {
            data.sort((a, b) => {
              if (a.id == this.currentUserId) return -1;
              if (b.id == this.currentUserId) return 1;
              return 0;
            });
          }
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

        if (this.approvingUserId === id) this.approvingUserId = null;
        if (this.rejectingUserId === id) this.rejectingUserId = null;

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
        if (this.approvingUserId === id) this.approvingUserId = null;
        if (this.rejectingUserId === id) this.rejectingUserId = null;
        this.error = "Terjadi kesalahan server.";
        setTimeout(() => (this.error = ""), 3000);
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

        this.isUpdatingInfo = false;

        if (res.ok) {
          this.openEditModal = false;
          this.message = data.message;
          await this.fetchUsers("approved");
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openEditModal = false;
          this.error = data.message || "Gagal memperbarui informasi.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.isUpdatingInfo = false;
        this.error = "Terjadi kesalahan server.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async deleteUser(id) {
      try {
        this.isDeletingUser = true;
        const res = await fetch(`/admin/users/delete/${id}`, {
          method: "DELETE",
        });
        const data = await res.json();

        this.isDeletingUser = false;

        if (res.ok) {
          this.openDeleteModal = false;
          this.message = data.message;
          await this.fetchUsers("approved");
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openDeleteModal = false;
          this.error = data.message || "Gagal menghapus user.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isDeletingUser = false;
        this.openDeleteModal = false;
        this.error = "Terjadi kesalahan server.";
        setTimeout(() => (this.error = ""), 3000);
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

        this.isDeletingRole = false;

        if (res.ok) {
          this.openRoleDeleteModal = false;
          this.message = data.message;
          await this.fetchRoles();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openRoleDeleteModal = false;
          this.error = data.message || "Gagal menghapus role.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        this.isDeletingRole = false;
        this.openRoleDeleteModal = false;
        this.error = "Terjadi kesalahan saat menghapus role.";
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

    toggleTrashUsers() {
      this.showTrashUsers = !this.showTrashUsers;
      if (this.showTrashUsers) this.fetchTrashUsers();
    },
    async fetchTrashUsers() {
      try {
        const response = await fetch('/admin/users/trash/data');
        const data = await response.json();
        this.trashUsers = data.users || [];
      } catch (error) {
        console.error('Error fetching trash data:', error);
      }
    },
    toggleTrashRoles() {
      this.showTrashRoles = !this.showTrashRoles;
      if (this.showTrashRoles) this.fetchTrashRoles();
    },
    async fetchTrashRoles() {
      try {
        const response = await fetch('/admin/roles/trash/data');
        const data = await response.json();
        this.trashRoles = data.roles || [];
      } catch (error) {
        console.error('Error fetching role trash data:', error);
      }
    },

    toggleSelectAllTrashUsers(e) {
      if (e.target.checked) {
        this.selectedTrashUsers = this.trashUsers.map(u => u.id);
      } else {
        this.selectedTrashUsers = [];
      }
    },
    restoreSelectedUsers() {
      this.trashType = 'user';
      this.restoreMode = 'multiple';
      this.openRestoreModal = true;
    },
    deletePermanentSelectedUsers() {
      this.trashType = 'user';
      this.deletePermanentMode = 'multiple';
      this.openDeletePermanentModal = true;
    },

    toggleSelectAllTrashRoles(e) {
      if (e.target.checked) {
        this.selectedTrashRoles = this.trashRoles.map(r => r.id);
      } else {
        this.selectedTrashRoles = [];
      }
    },
    restoreSelectedRoles() {
      this.trashType = 'role';
      this.restoreMode = 'multiple';
      this.openRestoreModal = true;
    },
    deletePermanentSelectedRoles() {
      this.trashType = 'role';
      this.deletePermanentMode = 'multiple';
      this.openDeletePermanentModal = true;
    },

    getTrashLabel() {
      return this.trashType === 'user' ? 'Pengguna' : 'Role';
    },
    getTrashItemName() {
      if (this.restoreMode === 'multiple' || this.deletePermanentMode === 'multiple') {
        return `${this.getTrashSelectedCount()} item dipilih`;
      }
      if (this.trashType === 'user') return this.selectedTrashItem?.nama_lengkap;
      return this.selectedTrashItem?.role_name;
    },
    getTrashSelectedCount() {
      return this.trashType === 'user' ? this.selectedTrashUsers.length : this.selectedTrashRoles.length;
    },

    confirmRestoreUser(id) {
      this.trashType = 'user';
      this.selectedTrashItem = this.trashUsers.find(u => u.id === id);
      this.restoreMode = 'single';
      this.openRestoreModal = true;
    },
    confirmDeletePermanentUser(id) {
      this.trashType = 'user';
      this.selectedTrashItem = this.trashUsers.find(u => u.id === id);
      this.deletePermanentMode = 'single';
      this.openDeletePermanentModal = true;
    },
    confirmRestoreRole(id) {
      this.trashType = 'role';
      this.selectedTrashItem = this.trashRoles.find(r => r.id === id);
      this.restoreMode = 'single';
      this.openRestoreModal = true;
    },
    confirmDeletePermanentRole(id) {
      this.trashType = 'role';
      this.selectedTrashItem = this.trashRoles.find(r => r.id === id);
      this.deletePermanentMode = 'single';
      this.openDeletePermanentModal = true;
    },

    async processRestore() {
      if (this.trashType === 'role') return this.processRestoreRole();

      this.isRestoring = true;
      try {
        let url = '/admin/users/restore';
        let body = {};

        if (this.restoreMode === 'single') {
          url += `/${this.selectedTrashItem.id}`;
        } else {
          body = { ids: this.selectedTrashUsers };
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
          await this.fetchTrashUsers();
          await this.fetchUsers('approved');
          await this.fetchUsers('pending');
          this.selectedTrashUsers = [];
          this.openRestoreModal = false;
          this.message = "Pengguna berhasil dipulihkan";
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openRestoreModal = false;
          const data = await response.json().catch(() => ({}));
          this.error = data.message || "Gagal memulihkan pengguna.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error('Error restoring data:', error);
      } finally {
        this.isRestoring = false;
      }
    },
    async processDeletePermanent() {
      if (this.trashType === 'role') return this.processDeletePermanentRole();

      this.isDeletingPermanent = true;
      try {
        let url = '/admin/users/deletePermanent';
        let body = {};

        if (this.deletePermanentMode === 'single') {
          url += `/${this.selectedTrashItem.id}`;
        } else {
          body = { ids: this.selectedTrashUsers };
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
          await this.fetchTrashUsers();
          this.selectedTrashUsers = [];
          this.openDeletePermanentModal = false;
          this.message = "Pengguna berhasil dihapus permanen";
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openDeletePermanentModal = false;
          const data = await response.json().catch(() => ({}));
          this.error = data.message || "Gagal menghapus pengguna permanen.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error('Error deleting data:', error);
      } finally {
        this.isDeletingPermanent = false;
      }
    },

    async processRestoreRole() {
      this.isRestoringRole = true;
      try {
        let url = '/admin/roles/restore';
        let body = {};

        if (this.restoreMode === 'single') {
          url += `/${this.selectedTrashItem.id}`;
        } else {
          body = { ids: this.selectedTrashRoles };
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
          await this.fetchTrashRoles();
          await this.fetchRoles();
          this.selectedTrashRoles = [];
          this.openRestoreModal = false;
          this.message = "Role berhasil dipulihkan";
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openRestoreModal = false;
          const data = await response.json().catch(() => ({}));
          this.error = data.message || "Gagal memulihkan role.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error('Error restoring role:', error);
      } finally {
        this.isRestoringRole = false;
      }
    },
    async processDeletePermanentRole() {
      this.isDeletingPermanentRole = true;
      try {
        let url = '/admin/roles/deletePermanent';
        let body = {};

        if (this.deletePermanentMode === 'single') {
          url += `/${this.selectedTrashItem.id}`;
        } else {
          body = { ids: this.selectedTrashRoles };
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
          await this.fetchTrashRoles();
          this.selectedTrashRoles = [];
          this.openDeletePermanentModal = false;
          this.message = "Role berhasil dihapus permanen";
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.openDeletePermanentModal = false;
          const data = await response.json().catch(() => ({}));
          this.error = data.message || "Gagal menghapus role permanen.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error('Error deleting role:', error);
      } finally {
        this.isDeletingPermanentRole = false;
      }
    },
  };
}
