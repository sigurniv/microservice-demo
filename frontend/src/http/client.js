const BASE_URL = process.env.VUE_APP_URL;

export const get = (url) => {
    return new Promise((resolve, reject) => {
        return fetch(BASE_URL + url).then(function (response) {
            resolve(response)
        }).catch(err => {
            reject(err)
        })
    })
}
