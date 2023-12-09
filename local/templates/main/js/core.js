(function () {
    'use strict';

    /******************************************************************************
    Copyright (c) Microsoft Corporation.

    Permission to use, copy, modify, and/or distribute this software for any
    purpose with or without fee is hereby granted.

    THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
    REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
    AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
    INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
    LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
    OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
    PERFORMANCE OF THIS SOFTWARE.
    ***************************************************************************** */
    /* global Reflect, Promise, SuppressedError, Symbol */


    function __awaiter(thisArg, _arguments, P, generator) {
        function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
        return new (P || (P = Promise))(function (resolve, reject) {
            function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
            function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
            function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
            step((generator = generator.apply(thisArg, _arguments || [])).next());
        });
    }

    typeof SuppressedError === "function" ? SuppressedError : function (error, suppressed, message) {
        var e = new Error(message);
        return e.name = "SuppressedError", e.error = error, e.suppressed = suppressed, e;
    };

    class Request {
        send(url, body, method = 'get') {
            return __awaiter(this, void 0, void 0, function* () {
                if (body) {
                    JSON.stringify(body);
                }
                const response = yield fetch(url);
                if (!response.ok) {
                    const message = `An error has occurred: ${response.status}`;
                    throw new Error(message);
                }
                return yield response.json();
            });
        }
    }

    /**
     * Класс применяет данные из запроса
     */
    //import $ from "jquery";
    class Template {
        apply(data) {
            // HTML
            if (data.html) {
                for (let key in data.html)
                    $(key).html(data.html[key]);
            }
            // Классы
            if (data.classes) {
                // Добавить класс к элементу
                if (data.classes.add)
                    for (let key in data.classes.add)
                        $(key).addClass(data.classes.add[key]);
                //Удалить класс у элемента
                if (data.classes.del)
                    for (let key in data.classes.del)
                        $(key).removeClass(data.classes.del[key]);
            }
            //Exec
            if (data.exec) {
                for (let key in data.exec) {
                    let execItem = data.exec[key];
                    if (typeof execItem === 'object') {
                        // if (execItem.params) {
                        //   setTimeout(execItem.execFunction, 10, ...execItem.params);
                        //   //setTimeout("test()", 10, ...execItem.params);
                        // } else {
                        setTimeout(execItem.execFunction, 10);
                        //}
                    }
                    else {
                        setTimeout(execItem, 10);
                    }
                }
            }
        }
    }
    // function test(param1: any, param2: any, param3: any) {
    //   console.log(param1)
    //   console.log(param2)
    //   console.log(param3)
    // }

    class Cart {
        constructor() {
            this.request = new Request();
            this.template = new Template();
        }
        //Добавление товара в корзину
        addDetail(productId, count = 1) {
            const result = this.request.send('/api/v1/cart/' + productId + '/' + count + '/add');
            result.then((result) => {
                if (result.data.template)
                    this.template.apply(result.data.template);
            });
        }
    }

    // @ts-ignore
    if (typeof jQuery === 'undefined') {
        throw new Error('US\'s JavaScript requires jQuery');
    }
    class Core {
        constructor() {
            this.cart = new Cart();
        }
    }
    if (!window.VZC) {
        window.VZC = new Core;
    }

})();
