import Request from "./modules/request";
import Cart from "./modules/cart";

class Core {
  request: Request
  cart: Cart

  constructor() {
    this.request = new Request()
    this.cart = new Cart()
  }

  // Добавление товара в корзину
  public addToCart(productId: number, count: number = 1) {
    let result = this.request.send('/api/v1/cart/' + productId + '/' + count + '/add')
    //console.log(result)
    return result
  }
}

let VZC = new Core;
//console.log(VZC.addToCart(918273, 2))

let button = document.querySelector('.header-account-badge');

button?.addEventListener("click", handleClick);

function handleClick(event: any) {
  event.preventDefault();
  let result = VZC.addToCart(14428, 2)

  return result.then((data) => {
    console.log(data)
  })

  //return false
}
