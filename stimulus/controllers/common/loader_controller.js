import { Controller } from "stimulus"

// parameters: max, url1, url2, url3, url4, url5, method1, method2, method3, method4, method4, append1, append2, append3, append4, append5
//      method1..5 defaults to POST
//      max, maximum count, default to -1 (unlimited)  (to limit the number of appends)
//      tokenName, name of the csrf token for POST's
//      append1..5 boolean, defaults to replace. For example, if append1 is present, it will append otherwise replace content.
// targets   : output1, output2, output3, output4, output5
export default class extends Controller {
  static targets = [ "output1" , "output2", "output3", "output4", "output5" ]

  // getFormState: return field values in target region.
  // Inspired by code from Simon Steinberger & Stefan Gabos found at:
  // Stackoverflow: https://stackoverflow.com/questions/11661187/form-serialize-javascript-no-framework
  getFormState(target, result) {
    // result.push('_csrfToken', encodeURIComponent(document.getElementsByName('_csrfToken')[0].value));
    if (typeof target === 'object')
        Array.prototype.slice.call(target.querySelectorAll('input[name]:not([disabled]),select[name]:not([disabled]),textarea[name]:not([disabled])')).forEach(function(control) {
            if (['file', 'reset', 'submit', 'button'].indexOf(control.type) === -1)
               if (control.type === 'select-multiple')
                  Array.prototype.slice.call(control.options).forEach(function(option) {
                    result.push(encodeURIComponent(control.name) + '=' + encodeURIComponent(option.value));
                  });
               else if (
                    ['checkbox', 'radio'].indexOf(control.type) === -1
                    || control.checked
                ) 
                   result.push(encodeURIComponent(control.name) + '=' + encodeURIComponent(control.value));
        });
    return result.join('&');
  }

  // return fetch promise
  loadContent(url, formData, method) {
      if ('GET' == method.toUpperCase()) 
         return fetch(url + (url.indexOf('?') > 0 ? '&' : '?') + formData)
      return fetch(url, {
           credentials: 'include'
         , method: method
         , headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              , 'X-CSRF-Token': encodeURIComponent(document.getElementsByName('_csrfToken')[0].value)
         }
         , body: formData.replace(/%20/g, '+')
      })
  }


  handleErrors(response) {
    if (!response.ok) {
        throw Error(response.statusText);
    }
    return response;
  }

  get count() {
    return parseInt(this.data.get("count")) || 1
  }

  set count(value) {
    this.data.set("count", value)
  }


  updateContent(target, varPostfix, formDataArray) {
      if (!target || !this.data.get('url' + varPostfix)) {
        return
      }
      let updateMethod;
      if (this.data.has('append' + varPostfix)) {
        updateMethod = (html) => target.insertAdjacentHTML( 'beforeend', html )
      } else {
        updateMethod = (html) => target.innerHTML = html
      }
      let formState = this.getFormState(target, formDataArray.slice(0))
      this.loadContent(this.data.get('url' + varPostfix), formState, this.data.get('method' + varPostfix) || 'POST')
      .then(this.handleErrors)
      .then(response => response.text())
      .then(updateMethod)
      .catch(error => console.log(error))
  }

  load(eventType, elem) {
    let formDataArray =  [ 'event=' + eventType, 'count=' + this.count++ ]
    if (elem && elem.id  ) formDataArray.push('elem=' + encodeURIComponent(elem.id))
    if (elem && elem.name) formDataArray.push(encodeURIComponent(elem.name) + '=' + encodeURIComponent(elem.value))
    if (this.hasOutput1Target) this.updateContent(this.output1Target, '1', formDataArray);
    if (this.hasOutput2Target) this.updateContent(this.output2Target, '2', formDataArray);
    if (this.hasOutput3Target) this.updateContent(this.output3Target, '3', formDataArray);
    if (this.hasOutput4Target) this.updateContent(this.output4Target, '4', formDataArray);
    if (this.hasOutput5Target) this.updateContent(this.output5Target, '5', formDataArray);
  }

  connect() {
    super.connect()
  }

  update(event) {
    event.preventDefault();
    let max = this.data.get('max') || -1
    if (max < 0 || this.count <= max) {
      this.load(event.type, event.currentTarget)
    } else if (max >= 0) {
      event.currentTarget.disabled = true
    }
  }


}
