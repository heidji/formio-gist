var languages = false;
host = document.currentScript.getAttribute('data-app') ?? '';
if(host !== '' && host.substr(host.length - 1) !== '/') host = host+'/';
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
        return Formio.fetch(host+'mongoadd.php', {
          body: JSON.stringify(submission),
          headers: {
            'content-type': 'application/json'
          },
          method: 'POST',
          mode: 'cors',
        })
          .then((response) => response.json())
          .then((json) => {
            if(typeof window.onFormioSubmit != 'undefined')
              window.onFormioSubmit(json)
            else
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
  fetch(host+'getlanguages.php')
    .then((response) => response.json())
    .then((json) => {
      languages = json
    })
    .catch((error) => {
      console.error(error);
    });
  fetch(host+'getformioconfig.php')
    .then((response) => response.json())
    .then((json) => {
      console.log(json);
      waitForLanguages(json);
    })
    .catch((error) => {
      console.error(error);
    });
}
