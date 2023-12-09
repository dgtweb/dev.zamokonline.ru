export default class Request {
  async send(url: string) {
    let response = await fetch(url);

    if (response.ok) {
      let json = await response.json();

      let elem = document.querySelector('.header-account-link .cart');
      // @ts-ignore
      elem.innerHTML = json.count

      console.log(json.api)
      console.log(json.project)
    } else {
      alert("Ошибка HTTP: " + response.status);
    }
  }
}
