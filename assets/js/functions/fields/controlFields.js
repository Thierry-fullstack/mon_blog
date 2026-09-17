
const controlFieldsLogin = function(champs, dial)
{
    let counter = 0;
    for(let i = 0; i < champs.length; i++)
    {
        if(champs[i].value !== '' && champs[i].classList.contains('is-valid') || champs[i].checked)
        {
            counter++;
        }
    }
    if(counter === champs.length || counter === champs.length -1 )
    {
        dial.textContent="Cliquez sur le bouton soumettre";
    }else if (counter !== champs.length)
    {
        dial.textContent="Saisir vos identifiants ";
    }
}

const controlFieldsRegistration = function(champs,dial)
{
    let counter = 0;
    for(let i = 0; i < champs.length; i++)
    {
        if(champs[i].value !== '' && champs[i].classList.contains('is-valid') || champs[i].checked && champs[i].classList.contains('is-valid'))
        {
            counter++;
        }
    }
    if(counter === champs.length)
    {
        dial.textContent="Cliquez sur le bouton soumettre";
    }else if (counter !== champs.length)
    {
        dial.textContent="Saisir vos coordonnées";
    }
}

export {controlFieldsLogin,controlFieldsRegistration}
