var login = new Vue({
    el: '#login',
    data: {
        errors: {
            username_required: false,
            password_required: false
        },
        statusLogin: 0,
    },
    methods: {
        login(event) {
            var action = $(event.target).attr('action');
            var data = new FormData($(event.target)[0]);

            if (this.validate()) {
                const _this = this;
                $(event.target).find('.panel').block({ 
                    message: '<i class="icon-spinner4 spinner"></i>',
                    timeout: 500, //unblock after 2 seconds
                    overlayCSS: {
                        backgroundColor: '#fff',
                        opacity: 0.8,
                        cursor: 'wait'
                    },
                    css: {
                        border: 0,
                        padding: 0,
                        backgroundColor: 'transparent'
                    }
                });
                axios.post(action, data).then(response => {
                    if (response.status === 200) {
                        _this.statusLogin = 2;
                        setTimeout(function () {
                            location.reload();
                        }, 300);
                    }
                }).catch(error => {
                    this.statusLogin = 1;
                });
            }
        },
        validate(e) {
            this.errors.username_required = ! $('[name=name]').val();
            this.errors.password_required = ! $('[name=password]').val();
            
            var error = 0;
            if (! $('[name=name]').val() || ! $('[name=password]').val())
                error++;

            return error === 0;
        }
    }
});