<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('formValidator', (initialData, rules) => ({
        ...initialData,
        errors: {},
        validate() {
            this.errors = {};
            for (const [field, fieldRules] of Object.entries(rules)) {
                // If the field is nested like roles[0], we'd need lodash get, but we'll stick to flat for now.
                let value = this[field];
                
                // Special case for role validation where department is required conditionally
                if (field === 'department' && rules.department && rules.department.requiredIf) {
                    if (this[rules.department.requiredIf.field] === rules.department.requiredIf.value) {
                        if (!value || String(value).trim() === '') {
                            this.errors[field] = rules.department.requiredIf.message || 'This field is required.';
                            continue;
                        }
                    }
                }

                if (!fieldRules || !Array.isArray(fieldRules)) continue;

                for (const rule of fieldRules) {
                    if (rule.type === 'required' && (!value || String(value).trim() === '')) {
                        if (rule.condition && !rule.condition()) continue;
                        if (Array.isArray(value) && value.length === 0) {
                            this.errors[field] = rule.message || 'This field is required.';
                            break;
                        } else if (!Array.isArray(value)) {
                            this.errors[field] = rule.message || 'This field is required.';
                            break;
                        }
                    }
                    if (rule.type === 'email' && value && String(value).trim() !== '') {
                        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!re.test(String(value))) {
                            this.errors[field] = rule.message || 'Invalid email format.';
                            break;
                        }
                    }
                    if (rule.type === 'min' && value && String(value).length < rule.value) {
                        this.errors[field] = rule.message || `Minimum length is ${rule.value}.`;
                        break;
                    }
                    if (rule.type === 'max' && value && String(value).length > rule.value) {
                        this.errors[field] = rule.message || `Maximum length is ${rule.value}.`;
                        break;
                    }
                    if (rule.type === 'confirmed' && value !== this[rule.target]) {
                        this.errors[field] = rule.message || 'Passwords do not match.';
                        break;
                    }
                }
            }
            return Object.keys(this.errors).length === 0;
        },
        submit(event) {
            if (!this.validate()) {
                event.preventDefault();
                
                // Find first error and scroll to it
                this.$nextTick(() => {
                    const firstError = document.querySelector('.border-red-500');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }
        }
    }));
});
</script>
