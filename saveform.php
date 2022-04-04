<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.form.io/formiojs/formio.full.min.css">
<script src="https://cdn.form.io/formiojs/formio.full.min.js"></script>

<!--div style="display: flex; flex-direction: row; justify-content: center">
    <div style="padding: 50px; display: flex; flex-direction: row; width: 90%; justify-content: space-between">
        <img onclick="setLanguage('de')"
             style="border: 1px solid black; cursor: pointer; height: auto;width: auto;max-width: 60px;max-height: 60px;"
             src="images/de.png" alt="">
        <img onclick="setLanguage('ua')"
             style="border: 1px solid black; cursor: pointer; height: auto;width: auto;max-width: 60px;max-height: 60px;"
             src="images/ua.png" alt="">
        <img onclick="setLanguage('en')"
             style="border: 1px solid black; cursor: pointer; height: auto;width: auto;max-width: 60px;max-height: 60px;"
             src="images/uk.png" alt="">
        <img onclick="setLanguage('ru')"
             style="border: 1px solid black; cursor: pointer; height: auto;width: auto;max-width: 60px;max-height: 60px;"
             src="images/ru.png" alt="">
    </div>
</div-->

<!--script>
  window.demo = {
    "data": {
      "familienname": "Mustermann",
      "vorname": "Max",
      "textField2": "",
      "day": "12\/12\/1987",
      "textField3": "Frankfurt aM",
      "ukrainischeRStaatsangehorigeR": "nein",
      "textField4": "Ledig",
      "dataGrid": [{
        "nameGebDatum": "Minni",
        "dateTime": "2022-03-02T00:00:00-06:00",
        "textField": "geflüchtet"
      }],
      "textField5": "Martina",
      "textField6": "Lange Strasse",
      "textField7": "60315",
      "textField8": "Frankfurt aM",
      "submit": true,
      "letzterAufenthaltsstatusInDerUkraine": "Student"
    }
  };
</script-->
<script>
    /*const changeLanguage = () => {
      $('#lang').onchange((e) => {

      })
    }*/
    $("#lang").on("change",
      function() {
        console.log('e');
      });
</script>
<div style="display: flex; flex-direction: row; justify-content: center; width: 100%">
    <div style="width: 80%">
        <label for="lang">Sprache</label>
        <select id="lang" style="margin-bottom: 20px" class="form-select" aria-label="Default select example">
            <option value="de" selected>Deutsch</option>
            <option value="ua">Ukrainisch</option>
            <option value="en">Englisch</option>
            <option value="ru">Russisch</option>
        </select>
        <!--div style="padding: 20px; background: blue; color: white; margin: 30px" onclick="setData(window.demo)">
            Eingabehilfe (DEMO)
        </div-->
        <hr>
        <div id="formio"></div>
    </div>
</div>
<script>
  window.onFormioSubmit = (json) => {
    window.location.href = '/formio/exit.php?id=' + json.id;
  }
</script>
<script type="text/javascript" src="embed.js" data-app="https://test.kleinanzeigen.mx/formio"></script>
<script>
  $("#lang").on("change",
    function() {
      setLanguage(this.value)
    });
</script>
