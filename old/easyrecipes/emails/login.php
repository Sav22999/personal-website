<html>
<head>
    <title>Easy Recipes</title>
    <meta charset="UTF-8">
    <style>
        a, td {
            font-family: sans-serif;
            text-decoration: none;
            color: #EEEEEE;
            margin: 5px;
        }

        a {
            text-decoration: underline;
        }

        td {
            font-size: 18px;
        }

        @import url('https://fonts.googleapis.com/css2?family=Source+Code+Pro&display=swap');

        #otp {
            background-color: #EEEEEE;
            color: #444444;
            font-family: "Source Code Pro";
            font-size: 20px;
            padding: 20px;
            padding-left: 30px;
            padding-right: 30px;
            border: 0px solid transparent;
            border-radius: 4px;
            text-align: center;
            cursor: text;
            user-select: text;
            display: inline-block;
        }
    </style>
</head>
<body style="border-radius:4px;border:1px solid #444444;width:auto;min-width:700px;max-width:700px;height:auto;overflow:hidden;">
<center>
    <table style="border-collapse:collapse;background-color:#444444;width:100%;min-width:700px;border:0px solid transparent;color:#EEEEEE;font-size:20px;">
        <tr>
            <td style="width:10px;border-radius:4px;"></td>
            <td style="padding-top:30px;padding-bottom:30px;border-radius:4px;text-align:center;">
                <img src="https://saveriomorelli.com/easyrecipes/images/logo-low-size.png" alt="Logo Easy Recipes"
                     height="60px"
                     style="border-radius:4px;"/>
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3" style="border-top:1px solid #ffffff;padding:20px;border-radius:4px;">
                Stai cercando di effettuare l'accesso al tuo account Easy Recipes.
                <br>
                Per completare l'accesso inserisci il seguente codice OTP nel relativo campo di testo.
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:center;">
                <div id="otp">
                    {{*{{otp_code}}*}}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding:20px;">
                Il codice OTP può essere usato solamente una volta e ha scadenza di 30 minuti.
            </td>
        </tr>
        <tr>
            <td colspan="3" style="border-top:1px solid #ffffff;padding:20px;border-radius:4px;">
                <small style="display: block;">
                    Easy Recipes è un progetto di <a href="https://saveriomorelli.com">Saverio Morelli</a>, non è un
                    marchio registrato; i contenuti sono di pubblico dominio o appartenenti a singoli individui.
                </small>
                <small>
                    Questa email è stata inviata automaticamente. Non rispondere ad essa perché questo indirizzo email
                    <b>non</b> è abilitato a ricevere email, ma solo a inviarle.
                </small>
                <small>
                    Se non sei stato tu a effettuare il login su EasyRecipes puoi tranquillamente ignorare questa email.
                </small>
            </td>
        </tr>
    </table>
</center>
</body>
</html>