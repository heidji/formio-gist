<html>
<head>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
            integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.form.io/formiojs/formio.full.min.css">
    <script src="https://cdn.form.io/formiojs/formio.full.min.js"></script>
</head>
<body>
<div style="display: flex; flex-direction: row; justify-content: center">
    <div style="padding: 50px; display: flex; flex-direction: row; width: 90%; justify-content: space-between">
        <img onclick="setLanguage('de')" style="border: 1px solid black; cursor: pointer; height: auto;width: auto;max-width: 60px;max-height: 60px;" src="/images/de.png" alt="">
        <img onclick="setLanguage('ua')" style="border: 1px solid black; cursor: pointer; height: auto;width: auto;max-width: 60px;max-height: 60px;" src="/images/ua.png" alt="">
        <img onclick="setLanguage('en')" style="border: 1px solid black; cursor: pointer; height: auto;width: auto;max-width: 60px;max-height: 60px;" src="/images/uk.png" alt="">
        <img onclick="setLanguage('ru')" style="border: 1px solid black; cursor: pointer; height: auto;width: auto;max-width: 60px;max-height: 60px;" src="/images/ru.png" alt="">
    </div>
</div>
<div style="padding: 20px; background: blue" onclick="setData(window.lol)">Eingabehilfe (DEMO)</div>
<div id="formio"></div>
<script type="text/javascript">
  var height = 0;
  var container = document.querySelector('#formio');
  window.lol = {
    "data": {
      "textField": "asd",
      "textField1": "adssd",
      "textField2": "sdf",
      "day": "12\/12\/1987",
      "textField3": "asdasd",
      "ukrainischeRStaatsangehorigeR": "nein",
      "textField4": "gsdf",
      "dataGrid": [{
        "nameGebDatum": "ycvswf",
        "dateTime": "2022-03-02T00:00:00-06:00",
        "textField": "ydfefd"
      }],
      "textField5": "saertfysdf",
      "textField6": "aesdfqef",
      "textField7": "12312",
      "textField8": "sfew",
      "file": [{
        "storage": "url",
        "name": "IMG-20210805-WA0002-03e5df75-c837-43c4-926b-0d9c48e73d02.jpg",
        "url": "\/formio\/upload.php?baseUrl=https%3A%2F%2Flvkwovhndcyuiqe.form.io&project=&form=\/IMG-20210805-WA0002-03e5df75-c837-43c4-926b-0d9c48e73d02.jpg",
        "size": 50798,
        "type": "image\/jpeg",
        "data": {
          "code": 1,
          "filename": "img-20210805-wa0002.jpg",
          "baseUrl": "https:\/\/lvkwovhndcyuiqe.form.io",
          "project": "",
          "form": ""
        },
        "originalName": "IMG-20210805-WA0002.jpg"
      }],
      "submit": true,
      "letzterAufenthaltsstatusInDerUkraine": "asdasdas"
    },
    "metadata": {
      "timezone": "America\/Mexico_City",
      "offset": -360,
      "origin": "https:\/\/hossidev.ligainsider.de",
      "referrer": "",
      "browserName": "Netscape",
      "userAgent": "Mozilla\/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit\/603.1.30 (KHTML, like Gecko) Version\/10.0 Mobile\/14E304 Safari\/602.1",
      "pathName": "\/api\/test10\/",
      "onLine": true
    }
  };
  var languages = false;
  function waitForLanguages(json){
    if (languages !== false){
      Formio.createForm(document.getElementById('formio'), json, {
        language: 'de',
        i18n: languages
      }).then((form) => {
        window.parent.postMessage({formio: true, height: container.scrollHeight}, '*');
        window.setLanguage = function (lang) {
          form.language = lang;
        };
        window.setData = function (data) {
          form.submission = {...data}
        }
        form.nosubmit = true;
        form.on('submit', function (submission) {
          //console.log(JSON.stringify(submission));
          window.lol = submission

          return Formio.fetch('/formio/mongoadd.php', {
            body: JSON.stringify(submission),
            headers: {
              'content-type': 'application/json'
            },
            method: 'POST',
            mode: 'cors',
          })
            .then((response) => response.json())
            .then((json) => {
              console.log(json);
            })
            .catch((error) => {
              console.error(error);
            });
        });
      });
    }
    else{
      setTimeout(() => waitForLanguages(json), 50);
    }
  }
  window.onload = function () {
    fetch('/formio/getlanguages.php')
      .then((response) => response.json())
      .then((json) => {
        languages = json
      })
      .catch((error) => {
        console.error(error);
      });
    fetch('/formio/getformioconfig.php')
      .then((response) => response.json())
      .then((json) => {
        console.log(json);
        waitForLanguages(json);
      })
      .catch((error) => {
        console.error(error);
      });
  }
</script>
</body>
</html>
