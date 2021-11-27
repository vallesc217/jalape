document.addEventListener('DOMContentLoaded', function () {
    /** Handle phone tel input text change */
    const phoneInputs = Array.from(document.querySelectorAll('.spoki-phone-container input[type="tel"]'));
    if (phoneInputs.length) {
        phoneInputs.forEach(c => c.addEventListener('keyup', function (e) {
            this.value = e.target.value.replace(/[^0-9]+/g, '');
        }))
    }

    /** Handle phone prefix input text change */
    const prefixInputs = Array.from(document.querySelectorAll('.spoki-phone-container .spoki-phone-prefix'));
    if (prefixInputs.length) {
        prefixInputs.forEach(c => c.addEventListener('keyup', function (e) {
            this.value = e.target.value.replace(/[^0-9\+]+/g, '');
            if (this.value.length && this.value[0] !== '+') {
                this.value = '+' + this.value;
            }
        }))
    }
})