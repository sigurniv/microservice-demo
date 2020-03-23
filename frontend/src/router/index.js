import VueRouter from 'vue-router'
import TagList from '@/components/Tag/TagList.vue'
import ArtistList from '@/components/Artist/ArtistList.vue'
import Artist from '@/components/Artist/Artist.vue'

const routes = [
    {
        path: '/',
        name: 'Tags',
        component: TagList
    },
    {
        path: '/:tag',
        name: 'Artists',
        component: ArtistList,
    },
    {
        path: '/:tag/:artist',
        name: 'Artist',
        component: Artist,
    },

]

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})

export default router
