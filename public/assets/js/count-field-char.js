function countFieldChar(input, inputResult, min, max = null) {
    input.addEventListener("input", function (event) {
        let length = event.target.value.length;
        input.classList.add("is-invalid");
        inputResult.classList.add("invalid-feedback");
        inputResult.innerText = length + ' caractères. (minimum ' + min + ' requis)';

        if (length >= min) {
            input.classList.replace("is-invalid", "is-valid")
            inputResult.classList.replace("invalid-feedback", "valid-feedback")
            inputResult.innerText = length + ' caractères';
        }

        if (max != null) {
            if (length > max) {
                input.classList.replace("is-valid", "is-invalid")
                inputResult.classList.replace("valid-feedback", "invalid-feedback")
                inputResult.innerText = length + ' caractères. (limite de ' + max + ' maximum)';
            }
        }
    });
}

