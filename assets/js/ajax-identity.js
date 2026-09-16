/**
 *
 * @typedef {Object} FormResponse
 * @property {string} code
 * @property {Object} errors
 * @property {string} html
 */
const form_civility = document.body.querySelector('#add_identity_form');
if(form_civility) {

    form_civility.addEventListener('submit', function (e) {
        e.preventDefault();
        fetch(this.action, {
            body: new FormData(e.target),
            method: 'POST',
        }).then(response => response.json())
            .then(json => {
                handleResponse(json)
            })
    });
}

    /**
    *
    * @param {FormResponse} response
    */
    const handleResponse = function (response){
        removeErrors();
        switch (response.code){
            case 'FORM_ADD_SUCCESSFULLY':
                form_civility.reset();
                window.location.href="https://localhost:8000/login";
                break;
            case 'FORM_BAD_RESPONSE':
                handleErrors(response.errors);
                break;

        }
    }
    const removeErrors = function(){
        const invalidFeedbackElements = document.querySelectorAll('.invalid-feedback');
        const isInvalidElements = document.querySelectorAll('.is-invalid');
        invalidFeedbackElements.forEach(errorElement => errorElement.remove());
        isInvalidElements.forEach(isInvalidElements => isInvalidElements.remove());
    }

    /**
    *
    * @param {Object} errors
    */
    const handleErrors = function(errors){
        if(errors.length === 0) return;
        for(const key in errors) {
            let element = document.querySelector(`#identity_${key}`);
        element.classList.add('is-invalid');
        let div = document.createElement('div');
        div.classList.add('invalid-feedback', 'd-block');
        div.innerText = errors[key];
        element.after(div);
    }
}


