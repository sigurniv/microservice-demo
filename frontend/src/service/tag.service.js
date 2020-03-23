import { get } from '@/http/client';

export const tagService = {
    async getTags() {
        return get('artist/tags/')
        .then((response) => response.json())
        .then((data) => {
            return data.data.tags;
        })
    }
}

