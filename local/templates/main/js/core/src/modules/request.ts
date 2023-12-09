export default class Request {

  async send(url: string, body?: Object, method: string = 'get') {

    let initParams: RequestInit = {
      method: method !== 'POST' && method !== 'post' ? 'GET' : 'POST',
      // cache: 'no-cache',
      // headers: {
      //   'Content-Type': 'application/json;charset=utf-8'
      // }
    }

    if (body) {
      initParams.body = JSON.stringify(body)
    }

    console.log(url);
    console.log(initParams)

    let response = await fetch(url)

    if (response.ok) {
      return await response.json() as Object
    } else {
      console.error("Ошибка HTTP: " + response.status + " (" + url + ")")
    }
  }
}
