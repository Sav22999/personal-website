<html>
<head>
    <title>Conversazione riaperta</title>
    <meta charset="UTF-8">
    <style>
        a, td {
            font-size: 18px;
            font-family: sans-serif;
            text-decoration: none;
            color: #ffffff;
            margin: 5px;
        }

        button {
            background-color: #ffffff;
            color: #3399ff;
            font-family: inherit;
            font-size: 20px;
            padding: 20px;
            padding-left: 30px;
            padding-right: 20px;
            border: 0px solid transparent;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
        }
    </style>
</head>
<body style="border-radius:4px;border:1px solid #3399ff;width:600px;min-width:700px;">
<center>
    <table style="border-collapse:collapse;background-color:#3399ff;width:100%;min-width:700px;border:0px solid transparent;color:#ffffff;font-size:20px;">
        <tr>
            <td style="width:10px;border-radius:4px;"></td>
            <td style="padding-top:30px;padding-bottom:30px;border-radius:4px;text-align:center;">
                <img src="https://saveriomorelli.com/images/emails/white.png" alt="Logo Saverio Morelli" height="30px"/>
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3" style="padding:20px;border-radius:4px;border-top:1px solid #ffffff;">
                La tua richiesta {{*{{ticket}}*}} è stata riaperta da un moderatore, fai
                clic sul seguente pulsante per visualizzarla.
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:center;">
                <a href="https://saveriomorelli.com/emails/?email={{*{{email}}*}}&ticket={{*{{ticket}}*}}">
                    <button>
                        Vai alla tua richiesta
                    </button>
                </a></td>
        </tr>
        <tr>
            <td colspan="3" style="padding:20px;">
                <a href="https://saveriomorelli.com/emails/?email={{*{{email}}*}}&ticket={{*{{ticket}}*}}">
                    In caso il pulsante non dovesse funzionare, copia e incolla questo link sul tuo web browser:
                    https://saveriomorelli.com/emails/?email={{*{{email}}*}}&ticket={{*{{ticket}}*}}
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="border-top:1px solid #ffffff;padding:20px;border-radius:4px;">
                <center>
                    <a href="https://www.saveriomorelli.com/">Home</a> &#8211; <a
                            href="https://www.saveriomorelli.com/about-me/">Chi sono</a> &#8211; <a
                            href="https://www.saveriomorelli.com/projects/">Progetti</a> &#8211; <a
                            href="https://www.saveriomorelli.com/contact-me/">Contatti</a> &#8211; <a
                            href="https://www.saveriomorelli.com/blog/">Blog</a>
                </center>
                <small>
                    Questa email è stata inviata automaticamente. Non rispondere ad essa perché questo indirizzo email
                    <b>non</b> è abilitato a ricevere email, ma solo a inviarle.
                </small>
            </td>
        </tr>
    </table>
</center>
</body>
</html>