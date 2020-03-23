import { get } from '@/http/client';

export const artistService = {
    async getArtists(tag) {
        return get(`artists/top/?tag=${tag}`)
        .then((response) => response.json())
        .then((data) => {
            console.log(data)
            return data.artists;
        })
    },
    async getArtist(name) {
        return get(`artist/?name=${name}`)
        .then((response) => response.json())
        .then((data) => {
            console.log(data)
            return data.artist;
        })
    }
}

