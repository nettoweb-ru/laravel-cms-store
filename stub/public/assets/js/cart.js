let Cart = {
    id: '',
    currency: '',
    url: {
        add: '/cart/add',
        clear: '/cart/clear',
        currency: '/cart/currency',
        remove: '/cart/remove'
    },

    add: function(itemId, onSuccess, onError, priceCode, quantity) {
        if (typeof priceCode === 'undefined') {
            priceCode = 'retail'
        }

        if (typeof quantity === 'undefined') {
            quantity = 1
        }

        let data = {
            itemId: itemId,
            priceCode: priceCode,
            quantity: quantity
        }

        this.send(this.url.add, data, onSuccess, onError)
    },

    clear: function(onSuccess, onError) {
        this.send(this.url.clear, {}, onSuccess, onError)
    },

    remove: function(cartItemId, onSuccess, onError, quantity) {
        if (typeof quantity === 'undefined') {
            quantity = null
        }

        let data = {
            cartItemId: cartItemId,
            quantity: quantity
        }

        this.send(this.url.remove, data, onSuccess, onError)
    },

    send: function(url, data, onSuccess, onError) {
        data.cartId = this.id
        Ajax.post(url, data, onSuccess, onError)
    },

    setCurrency: function(currencyCode, onSuccess, onError) {
        let data = {
            currencyCode: currencyCode
        }

        this.send(this.url.currency, data, onSuccess, onError)
    }
}
