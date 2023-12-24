/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

//Jquery
const $ = require('jquery');
global.$ = global.jQuery = $;

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
/* Add scrollbar effect */
const elems = document.querySelectorAll("div.hide-div")
const callback = (entries, observer) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add("fadeIn")
        }
    })
}

const options = {}

const myObserver = new IntersectionObserver(callback, options)

elems.forEach(e=>{
    myObserver.observe(e);
})

// desactiver inspect element en click droit
/*document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});*/
// desactiver inspect element en clavier
/*document.addEventListener("keydown", function(e) {
    //document.onkeydown = function(e) {
    // "I" key
    if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
        disabledEvent(e);
    }
    // "J" key
    if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
        disabledEvent(e);
    }
    // "S" key + macOS
    if (e.keyCode == 83 && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) {
        disabledEvent(e);
    }
    // "U" key
    if (e.ctrlKey && e.keyCode == 85) {
        disabledEvent(e);
    }
   // "F12" key
     if (event.keyCode == 123) {
         disabledEvent(e);
     }
    // "C" key
    if (e.ctrlKey && event.keyCode == 67) {
        disabledEvent(e);
    }
}, false);
function disabledEvent(e){
    if (e.stopPropagation){
        e.stopPropagation();
    } else if (window.event){
        window.event.cancelBubble = true;
    }
    e.preventDefault();
    return false;
}*/
function getActualite(){
    $.ajax({
        type: "GET",
        url: "/ajax/sinistres/actualite",
        dataType: "json",
        encode: true,
    }).done(function (data) {
       const formattedNumber= Math.round(data.montant).toLocaleString(undefined, {maximumFractionDigits: 0,style: 'currency', currency: 'EUR' })
        $('#sinistre-montant').removeClass('loading h-12')
        $('#nombre-sinistre').removeClass('loading h-12');
        $('#sinistre-montant').text(formattedNumber);
        $('#nombre-sinistre').text(data.nombre);
    });
}
window.addEventListener('load', function() {
    getActualite();    

    $(document).ready(function () {
        $("#form_footer").submit(function (event) {
            event.preventDefault();
            
            grecaptcha.ready(function() {
                
                grecaptcha.execute('6Lfw5jYlAAAAAD8EtCHiX4BWmuj-udPYrDJKQgVK', {action: 'submit'}).then(function(token) {
                    
                    $('#modal_chargement').css("display", "block")
                    $.ajax({
                        type: "POST",
                        url: "/contact",
                        data: $('#form_footer').serialize(),
                        dataType: "json",
                        encode: true,
                    }).done(function (data) {
                        var returnedData = JSON.parse(data);
                        
                        $("#name").val('')
                        $("#tel").val('')
                        $("#email").val('')
                        $("#immat").val('')
                        $("#message").val('')
                        $('#modal_chargement').css("display", "none")
                        $('#confirmation_contact').css("display", "block")
                    });
                    
                });
            });

            event.preventDefault();
        });
    });

/*     $(document).ready(function () {
        $("#form_footer").submit(function (event) {
           event.preventDefault();

                $('#modal_chargement').css("display", "block")    
                     $.ajax({
                    type: "POST",
                    url: "/contact",
                    data: $('#form_footer').serialize(),
                    dataType: "json",
                    encode: true,
                    }).done(function (data) {
                    $('#confirmation_contact').css("display", "block")
                    $('#modal_chargement').css("display", "none")
                        $("#name").val('')
                        $("#phone").val('')
                        $("#email").val('')
                        $("#immat").val('')
                        $("#message").val('')
                    });

        });
    }); */
})

function verifieNum(event, leChamp) {
    var keyCode = event.which ? event.which : event.keyCode;
    var touche = String.fromCharCode(keyCode);

    var champ = document.getElementById(leChamp);
    var val = champ.value

    var caracteres = '0123456789';

    if(caracteres.indexOf(touche) >= 0 && element.value.length < 10) {
        return true
    }else{
        return false
    }
}
function verifPassword(event,champ){
    let parameters = {
        count : false,
        min : false,
        maj : false,
        number : false,
        special : false,
    }

    let password = document.getElementById(champ).value;
    let strengthBar = document.getElementById('strengthbarPassword');
    let textStrength = document.getElementById('textStrengthPassword');


    parameters.min = (/[a-z]+/.test(password))?true:false;
    parameters.maj = (/[A-Z]+/.test(password))?true:false;
    parameters.numbers = (/[0-9]+/.test(password))?true:false;
    parameters.special = (/[!\"$%&/()=?@~`\\.\';:+=^*_-]+/.test(password))?true:false;
    parameters.count = (password.length > 7)?true:false;
    if (password.length != 0){
        strengthBar.style.display = 'block';
    } else {
        strengthBar.style.display = 'none';
    }
    strengthBar.innerHTML = '';
    let barLength = Object.values(parameters).filter(value=>value);
    for (let i in barLength){
        let span = document.createElement('span');
        span.classList.add('strengthPassword');
        if(i == 4){
            span.style.borderRadius = '0 0 5px 0';
        }

        if(i == 0){
            span.style.borderRadius = '0 0 0 5px';
        }
        strengthBar.appendChild(span);
    }

    let spanRef = document.getElementsByClassName('strengthPassword' );
    for(let i = 0; i < spanRef.length; i++){
        switch(spanRef.length - 1){
            case 0 :
                spanRef[i].style.background = '#C70039';
                textStrength.style.color = '#C70039';
                textStrength.innerHTML = "Très faible"
                break;

            case 1 :
                spanRef[i].style.background = '#FF5733';
                textStrength.style.color = '#FF5733';
                textStrength.innerHTML = "Faible"
                break;

            case 2 :
                spanRef[i].style.background = '#FFC300';
                textStrength.style.color = '#FFC300';
                textStrength.innerHTML = "Bien"
                break;

            case 3 :
                spanRef[i].style.background = '#7ABC4D';
                textStrength.style.color = '#7ABC4D';
                textStrength.innerHTML = "Fort"
                break;

            case 4 :
                spanRef[i].style.background = '#6366F1';
                textStrength.style.color = '#6366F1';
                textStrength.innerHTML = "Très fort"
                break;
        }
    }



}
function closeModal(myModal){
    $('#'+myModal).css("display", "none")
}
function verifieImmat(event, leChamp) {

    var keyCode = event.which ? event.which : event.keyCode;
    var touche = String.fromCharCode(keyCode);

    var champ = document.getElementById(leChamp);

    var caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-';
    console.log('test');
    if(caracteres.indexOf(touche) >= 0) {
        return true
    }else{
        return false
    }
}
$('.form').each(function() {
    $(this).on('keypress blur invalid', function() {
        if ($(this).is(':valid') == false) {
            $(this).addClass('form-invalid')
            $('.' + $(this).attr('id')).removeClass('hidden')
        } else {
            $(this).removeClass('form-invalid')
            $('.' + $(this).attr('id')).addClass('hidden')
        }
    })
})

$('#form_bic').focusout(function () {

    var bic = $(this).val()

    if (bic.length >= 8) {

        var formData = {
            bic: $(this).val()
        }

        $.ajax({
            type: "POST",
            url: "/bic",
            data: formData,
            dataType: "json",
            encode: true
        }).done(function (data) {
            var returnedData = JSON.parse(data);
            if (returnedData.response != "Ok") {
                $("#form_create_bic").addClass('form-message')
                $("span.form_bic").addClass("span-message")
                $("span.form_bic").text("Le BIC semble erroné ou inconnu")
                $("span.form_bic").removeClass("hidden")
            } else {
                $("#form_create_bic").removeClass('form-message')
                $("span.form_bic").removeClass("span-message")
                $("span.form_bic").addClass("hidden")
                $("span.form_bic").text("*Ce champ est obligatoire")
            }
        });

    } else if (bic.length < 8) {
        $("#form_bic").removeClass('form-message')
        $("span.form_bic").removeClass("span-message")
        $("span.form_bic").addClass("hidden")
        $("span.form_bic").text("*Ce champ est obligatoire")
    }

})

$("#form_pwd").submit(function (event) {
event.preventDefault();
$('#loading').removeClass('hidden');
$('#form_pwd').addClass('hidden');
            var formData = {
                email: $("#form_email").val(),
            };

            $.ajax({
                type: "POST",
                url: "/pwd",
                data: formData,
                dataType: "json",
                encode: true,
            }).done(function (data){
                var returnedData = JSON.parse(data);
                if (returnedData.response != "Ok") {
                    $('#loading').addClass('hidden');
                    $('#success').removeClass('hidden');
                }
                else{
                    $('#loading').addClass('hidden');
                    $('#error').removeClass('hidden');
                }
    })

});
$('.date-check').focusout(function (){
    let day=$('#form_day').val();
    let month=$('#form_month').val();
    let year= $('#form_year').val();
    let formData={
        day,
        month,
        year
    }
    $.ajax({
        type: "POST",
        url: "/ajax/check/date",
        data: formData,
        dataType: "json",
        encode: true
    }).done(function (data) {
        var returnedData = JSON.parse(data);
        if (returnedData.response == "OK") {

            $("#form_check_date").removeClass("span-message")
            $("#form_check_date").addClass("hidden")
            $("#form_check_date").text("*Ce champ est obligatoire")
        } else {
            $("#form_check_date").addClass("span-message")
            $("#form_check_date").text("La date est invalide")
            $("#form_check_date").removeClass("hidden")
        }
    }).fail(function () {
        $("#form_check_date").addClass("span-message")
        $("#form_check_date").text("La date est invalide")
        $("#form_check_date").removeClass("hidden")
    });
})
function dateIsValid(date) {
    return date instanceof Date && !isNaN(date);
}
$('#form_iban').focusout(function () {

    var iban = $(this).val()

    if (iban.length >= 8) {

        var formData = {
            iban: $(this).val()
        }

        $.ajax({
            type: "POST",
            url: "/iban",
            data: formData,
            dataType: "json",
            encode: true
        }).done(function (data) {
            var returnedData = JSON.parse(data);
            if (returnedData.response != "Ok") {
                $("#form_create_iban").addClass('form-message')
                $("span.form_iban").addClass("span-message")
                $("span.form_iban").text("L'IBAN semble erroné ou inconnu")
                $("span.form_iban").removeClass("hidden")
            } else {
                $("#form_iban").removeClass('form-message')
                $("span.form_iban").removeClass("span-message")
                $("span.form_iban").addClass("hidden")
                $("span.form_iban").text("*Ce champ est obligatoire")
            }
        }).fail(function () {
            $("#form_create_iban").addClass('form-message')
            $("span.form_iban").addClass("span-message")
            $("span.form_iban").text("L'IBAN semble erroné ou inconnu")
            $("span.form_iban").removeClass("hidden")
        });

    } else {
        $("#form_iban").removeClass('form-message')
        $("span.form_iban").removeClass("span-message")
        $("span.form_iban").text("*Ce champ est obligatoire")
    }

})

function verifieCaracteresEtNum(event, leChamp) {

    var keyCode = event.which ? event.which : event.keyCode;
    var touche = String.fromCharCode(keyCode);

    var champ = document.getElementById(leChamp);

    var caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    if(caracteres.indexOf(touche) >= 0) {
        return true
    }else{
        return false
    }
}

window.verifPassword=verifPassword;
window.closeModal = closeModal;
window.verifieNum = verifieNum;
window.verifieImmat = verifieImmat;
window.verifieCaracteresEtNum = verifieCaracteresEtNum;

