<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Neustar PHP Coding Assignment - Eduardo Chongkan</div>
 
                    <div class="card-body">
                        <form @submit="checkForm">
                            <p v-if="errors.length">
                                <b>Please correct the following error(s):</b>
                                <ul>
                                <li v-for="error in errors" :key="error">{{ error }}</li>
                                </ul>
                            </p>
                        <strong>Enter one or more DOMAIN(s), separated by comma.</strong>
                        <textarea class="form-control" v-model="domains"></textarea>
                        <p v-if="invalidDomains.length">
                                <b>Invalid Domains(s):</b>
                                 <ul>
                                    <li v-for="invalidDomain in invalidDomains" :key="invalidDomain">{{ invalidDomain }}</li>
                                </ul>
                        </p>
                        <button class="btn btn-success">Lookup</button>
                        </form>
                        <p><strong>BackEnd AJAX Response:</strong></p>
                        
                        <pre>
                        {{output}}
                        </pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
 
<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data() {
            return {
              domains: 'chongkan.com, neustar.com', 
              output: '', 
              errors: [], 
              invalidDomains: [], 
              validDomains: []
            };
        },
        methods: {

            /**
             * 
             * @param {*} e 
             */
            checkForm: function (e) {
                e.preventDefault();
                this.output = '';
                this.errors = [];
                this.invalidDomains = [];
                this.validDomains = [];
                if (!this.domains || !this.validateDomains(this.domains)) {
                    this.errors.push("Please enter one or more DOMAIN(s), separated by Commas. e.g. php.net, neustar.com");
                } 
                if (!this.errors.length) {
                    this.formSubmit();
                    return true;
                }
            }, 
            
            /**
             * Splits the string by commas insto an array of IPs and validates each of the IPs. 
             * @param {string} domains 
             */
            validateDomains: function (domains) {
                let domainList = domains.split(',');
                let isValid = true;

                let validateDomain = (domain) => {
                    let re = /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/;
                    return re.test(domain);
                }

                domainList.forEach((domain) => {
                    domain = domain.trim();
                    if (!validateDomain(domain)){
                        isValid =  false;
                        this.invalidDomains.push(domain);
                    }else{
                        this.validDomains.push(domain);
                    }
                    
                }, this);
                return isValid;
                
            },

             /**
             * Uses Axios to handle HTTP Requests
             * 
             */
            formSubmit() {
                let currentObj = this;

                axios.post('/api/domainLookup', {
                    domains: this.validDomains
                })
                .then(function (response) {
                    currentObj.output = response.data;
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
            }
        }
    }
</script>