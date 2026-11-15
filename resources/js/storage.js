import { createStore } from 'vuex'

export const store = new createStore({
    state: {
      cart_count: 0,
      cart_tovars: {},
      cart_open: false,

      favorites_count: 0,
      favorites_tovars: {}
    },

    mutations: {
        setCount (state, value) {
            state.cart_count = value
        },

        setTovars (state, value) {
            state.cart_tovars = value
        },

        openCart (state) {
            state.cart_open = true
        },

        closeCart (state) {
            state.cart_open = false
        },


        setFavoritesCount (state, value) {
            state.favorites_count = value
        },

        setFavorites (state, value) {
            state.favorites_tovars = value
        },
    },

    getters: {
        cartCount: state => {
          return state.cart_count
        },

        favoritesCount: state => {
          return state.favorites_count
        },

        favoritesList: state => {
            return state.favorites_tovars
        }
    },

    actions: {

        initialBascet(context, value) {
                axios.get('/bascet/get')
                .then((response) => {
                    console.log(response.data.count)
                    context.commit('setCount', response.data.count)
                    context.commit('setTovars', response.data.position)
                })
                .catch(error => console.log(error));
        },

        // Изменение количества позиции в корзине
        updateCartItem(context, { sku, count }) {
            axios.post('/bascet/update', {
                product_id: sku,
                count: count,
                _token: document.querySelector('meta[name="_token"]').content
            })
            .then(() => context.dispatch('initialBascet'))
            .catch(error => console.log(error));
        },

        // Удаление позиции из корзины
        deleteCartItem(context, sku) {
            axios.delete('/bascet/delete', {
                data: {
                    _token: document.querySelector('meta[name="_token"]').content,
                    product_id: sku
                }
            })
            .then(() => context.dispatch('initialBascet'))
            .catch(error => console.log(error));
        },

        initialFavorites(context, value) {
            axios.get('/favorites/get')
            .then((response) => {
                console.log(response.data.count)
                context.commit('setFavoritesCount', response.data.count)
                context.commit('setFavorites', response.data.position)
            })
            .catch(error => console.log(error));
        }
      }
  })
