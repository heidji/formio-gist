var languages = false;
function waitForLanguages(json){
  if (languages !== false){
    Formio.createForm(document.getElementById('formio'), json, {
      language: 'de',
      i18n: languages
    }).then((form) => {
      window.setLanguage = function (lang) {
        form.language = lang;
      };
      window.setData = function (data) {
        form.submission = {...data}
      }
      form.nosubmit = true;
      form.on('submit', function (submission) {
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
            window.location.href = '/formio/getstatus.php?id='+json.id;
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
