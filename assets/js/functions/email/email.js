
/* check email login & register */
const controlEmail = function (champ,info) {
    const regexMail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const emailRegex = new RegExp(regexMail);
    return champ.value.match(emailRegex)
}

export {controlEmail};
