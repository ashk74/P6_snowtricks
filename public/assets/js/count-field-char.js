function countFieldChar(input, inputResult, min, max = null) {
    input.addEventListener("input", function (event) {
        let length = event.target.value.length;
        input.classList.add("is-invalid");
        inputResult.classList.add("invalid-feedback");
        inputResult.innerText = length + " caractères. (minimum " + min + " requis)";

        if (length >= min) {
            input.classList.replace("is-invalid", "is-valid")
            inputResult.classList.replace("invalid-feedback", "valid-feedback")
            inputResult.innerText = length + " caractères";
        }

        if (max != null) {
            if (length > max) {
                input.classList.replace("is-valid", "is-invalid")
                inputResult.classList.replace("valid-feedback", "invalid-feedback")
                inputResult.innerText = length + " caractères. (limite de " + max + " maximum)";
            }
        }
    });
}

if (document.body.contains(document.getElementById("registration_form_fullname"))) {
    let registrationFullname = document.getElementById("registration_form_fullname");
    let registrationFullnameResult = document.getElementById("registration-fullname-result");
    registrationFullname.oninput = countFieldChar(registrationFullname, registrationFullnameResult, 5);

    let registrationPasswordFirst = document.getElementById("registration_form_password_first");
    let registrationPasswordFirstResult = document.getElementById("registration-password-first-result");
    registrationPasswordFirst.oninput = countFieldChar(registrationPasswordFirst, registrationPasswordFirstResult, 8);

    let registrationPasswordSecond = document.getElementById("registration_form_password_second");
    let registrationPasswordSecondResult = document.getElementById("registration-password-second-result");
    registrationPasswordSecond.oninput = countFieldChar(registrationPasswordSecond, registrationPasswordSecondResult, 8);
}

if (document.body.contains(document.getElementById("trick_name"))) {
    let trickName = document.getElementById("trick_name");
    let trickNameResult = document.getElementById("trick-name-result");
    trickName.oninput = countFieldChar(trickName, trickNameResult, 3);

    let trickContent = document.getElementById("trick_content");
    let trickContentResult = document.getElementById("trick-content-result");
    trickContent.oninput = countFieldChar(trickContent, trickContentResult, 50);
}
