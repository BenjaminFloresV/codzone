

document.addEventListener('DOMContentLoaded', () =>{

    descImg.onchange = evt => {
        const [file] = descImg.files
        if (file) {
            descImgTarget.src = URL.createObjectURL(file)
        }
    }

    footerImg.onchange = evt => {
        const [file] = footerImg.files
        if (file) {
            footerImgTarget.src = URL.createObjectURL(file)
        }
    }

    extraImg.onchange = evt => {
        const [file] = extraImg.files
        if (file) {
            extraImgTarget.src = URL.createObjectURL(file)
        }
    }


});
