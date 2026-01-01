function loginApp() {
    return {
        email: '',
        password: '',
        loading: false,
        message: '',
        error: '',
        errors: {},
        show: false,

        async submit() {
            this.loading = true; this.message = ''; this.error = ''; this.errors = {};
            try {
                let form = new FormData();
                form.append('email', this.email);
                form.append('password', this.password);
                
                const res = await fetch('/masuk', {
                    method: 'POST', body: form,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                
                if (data.csrf_token) document.querySelectorAll('input[name="csrf_test_name"]').forEach(e => e.value = data.csrf_token);
                
                if (data.success) {
                    this.loading = false;
                    this.message = data.message;
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    this.loading = false;
                    this.error = data.message;
                    if (data.errors) this.errors = data.errors;
                    setTimeout(() => (this.error = ""), 3000);
                }
            } catch (e) {
                this.loading = false;
                this.error = "Terjadi kesalahan server.";
                setTimeout(() => (this.error = ""), 3000);
            }
        }
    };
}

function registerApp() {
    return {
        nama_lengkap: '',
        email: '',
        password: '',
        loading: false,
        message: '',
        error: '',
        errors: {},
        show: false,

        async submit() {
            this.loading = true; this.message = ''; this.error = ''; this.errors = {};
            try {
                let form = new FormData();
                form.append('nama_lengkap', this.nama_lengkap);
                form.append('email', this.email);
                form.append('password', this.password);

                const res = await fetch('/daftar', {
                    method: 'POST', body: form,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.csrf_token) document.querySelectorAll('input[name="csrf_test_name"]').forEach(e => e.value = data.csrf_token);

                if (data.success) {
                    this.loading = false;
                    this.message = data.message;
                    this.nama_lengkap = '';
                    this.email = '';
                    this.password = '';
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    this.loading = false;
                    this.error = data.message;
                    if (data.errors) this.errors = data.errors;
                    setTimeout(() => (this.error = ""), 3000);
                }
            } catch (e) {
                this.loading = false;
                this.error = "Terjadi kesalahan server.";
                setTimeout(() => (this.error = ""), 3000);
            }
        }
    };
}

function passwordApp() {
    return {
        email: '',
        password: '',
        passwordConfirmation: '',
        loading: false,
        message: '',
        error: '',
        errors: {},
        show: false,

        async sendLink() {
            this.loading = true; this.message = ''; this.error = ''; this.errors = {};
            try {
                let form = new FormData();
                form.append('email', this.email);
                const res = await fetch('/lupa-password', {
                    method: 'POST', body: form,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.csrf_token) document.querySelectorAll('input[name="csrf_test_name"]').forEach(e => e.value = data.csrf_token);
                if (data.success) {
                    this.message = data.message;
                    this.email = '';
                    setTimeout(() => (this.message = ""), 5000);
                } else {
                    this.error = data.message;
                    if (data.errors) this.errors = data.errors;
                    setTimeout(() => (this.error = ""), 3000);
                }
            } catch (e) {
                this.error = "Terjadi kesalahan server.";
                setTimeout(() => (this.error = ""), 3000);
            } finally {
                this.loading = false;
            }
        },
        async resetPass(token) {
            this.loading = true; this.message = ''; this.error = ''; this.errors = {};
            try {
                let form = new FormData();
                form.append('token', token);
                form.append('password', this.password);
                form.append('password_confirmation', this.passwordConfirmation);
                const res = await fetch('/reset-password', {
                    method: 'POST', body: form,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.csrf_token) document.querySelectorAll('input[name="csrf_test_name"]').forEach(e => e.value = data.csrf_token);
                if (data.success) {
                    this.message = data.message;
                    if (data.redirect) setTimeout(() => window.location.href = data.redirect, 1500);
                } else {
                    this.error = data.message;
                    if (data.errors) this.errors = data.errors;
                    setTimeout(() => (this.error = ""), 3000);
                }
            } catch (e) {
                this.error = "Terjadi kesalahan server.";
                setTimeout(() => (this.error = ""), 3000);
            } finally {
                this.loading = false;
            }
        }
    };
}
