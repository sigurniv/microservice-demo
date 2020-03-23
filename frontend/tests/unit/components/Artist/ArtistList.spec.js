import ArtistList from '@/components/Artist/ArtistList'
import { shallowMount } from '@vue/test-utils'
import flushPromises from 'flush-promises'
import {getLocalVue, getStore} from "../../helper";
import router from '@/router'

const localVue = getLocalVue()
const store = getStore()

const artistService = {
    getArtists() {
        return new Promise((resolve) => {
            resolve([
                {
                    "name": "name1",
                    "image": "image1",
                    "id": "id1"
                },
                {
                    "name": "name2",
                    "image": "image2",
                    "id": "id2"
                }
            ])
        })
    }
}

describe('ArtistList.vue', () => {
    it('should render artists', async () => {
        const tag = "tag"
        const wrapper = shallowMount(ArtistList, {
            propsData: { artistService },
            store,
            localVue,
            router,
            stubs: ['router-link', 'router-view'],
            mocks: {
                $route: {
                    params: { tag }
                }
            }
        })

        await flushPromises()
        expect(wrapper.findAll(".image").length).toBe(2)
        expect(wrapper.findAll(".image").at(0).find('img').attributes().src).toBe("//image1")
        expect(wrapper.findAll(".image").at(0).find('.caption').text()).toBe("name1")
        expect(wrapper.findAll(".image").at(1).find('img').attributes().src).toBe("//image2")
        expect(wrapper.findAll(".image").at(1).find('.caption').text()).toBe("name2")
    })

    it('should render correct header', async() => {
        const tag = "tag"
        const wrapper = shallowMount(ArtistList, {
            propsData: { artistService },
            store,
            localVue,
            router,
            stubs: ['router-link', 'router-view'],
            mocks: {
                $route: {
                    params: { tag }
                }
            }
        })

        await flushPromises()
        const header = wrapper.findAll(".header").at(0);
        expect(header).toBeTruthy()
        const links = header.findAll('router-link-stub')
        expect(links.length).toBe(2)
        expect(links.at(0).attributes().to).toBe("/")
        expect(links.at(1).attributes().to).toBe(tag)
    })
})
