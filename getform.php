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
<div id="formio"></div>
<script type="text/javascript">

  var languages = false;
  function waitForLanguages(json){
    if (languages !== false){
      Formio.createForm(document.getElementById('formio'), json, {
        //readOnly: true,
        language: 'en',
        i18n: languages
      }).then((form) => {
        console.log(form);
        window.parent.postMessage({formio<?= $_GET['id'] ?>: true, height: container.scrollHeight}, '*');
        window.setLanguage = function (lang) {
          form.language = lang;
        };
        window.setData = function (data) {
          form.submission = {...data}
        }
          <?php if(isset($_GET['id'])): ?>
        Formio.fetch('/formio/mongoget.php', {
          body: JSON.stringify({id: '<?= $_GET['id'] ?>'}),
          headers: {
            'content-type': 'application/json'
          },
          method: 'POST',
          mode: 'cors',
        })
          .then((response) => response.json())
          .then((json) => {
            if (json.code === 1) {
              setData(json.data);
            } else {
              $('#formio').html('<b>STATUS: NICHT GEFUNDEN</b>')
            }
          })
          <?php endif; ?>
        form.nosubmit = true;
        form.on('submit', function (submission) {
          return Formio.fetch('/formio/mongoedit.php', {
            body: JSON.stringify(submission),
            headers: {
              'content-type': 'application/json'
            },
            method: 'POST',
            mode: 'cors',
          })
            .then((response) => response.json())
            .then((json) => {
              setData(submission);
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

  var height = 0;
  var container = document.querySelector('#formio');
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

  var observeDOM = (function () {
    var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

    return function (obj, callback) {
      if (!obj || obj.nodeType !== 1) return;

      if (MutationObserver) {
        // define a new observer
        var mutationObserver = new MutationObserver(callback)

        // have the observer observe foo for changes in children
        mutationObserver.observe(obj, {childList: true, subtree: true})
        return mutationObserver
      }

      // browser support fallback
      else if (window.addEventListener) {
        obj.addEventListener('DOMNodeInserted', callback, false)
        obj.addEventListener('DOMNodeRemoved', callback, false)
      }
    }
  })()
  observeDOM(container, function (m) {
    if (container.scrollHeight != height) {
      if (height != 0)
        window.parent.postMessage({formio<?= $_GET['id'] ?>: true, height: container.scrollHeight}, '*');
      height = container.scrollHeight
    }
  });

  /*$(document).bind('DOMSubtreeModified', function(e) {
    if(document.body.scrollHeight != height){
      if(height != 0)
        window.parent.postMessage({formio: true, height: document.body.scrollHeight}, '*');
      height = document.body.scrollHeight
    }
  });*/
</script>
</body>
</html>
