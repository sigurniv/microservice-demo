import TagList from '@/components/Tag/TagList'
import { shallowMount } from '@vue/test-utils'
import flushPromises from 'flush-promises'
import {getLocalVue, getStore} from "../../helper";

const localVue = getLocalVue()
const store = getStore()

const tagService = {
    getTags() {
        return new Promise((resolve) => {
            resolve([
                "test1",
                "test2",
            ])
        })
    }
}

describe('TagList.vue', () => {
    it('should render tags', async () => {
        const wrapper = shallowMount(TagList, {
            propsData: { tagService },
            store,
            localVue
        })

        await flushPromises()
        expect(wrapper.findAll("li").length).toBe(2)
        expect(wrapper.findAll("li").at(0).text()).toBe("test1")
        expect(wrapper.findAll("li").at(1).text()).toBe("test2")
    })
})
