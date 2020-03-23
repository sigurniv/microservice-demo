<template>
  <div class="container">
    <ul v-if="!loading">
      <li v-for="tag in tags" v-bind:key="tag" @click="navigateArtists(tag)">
        {{tag}}
      </li>
    </ul>
  </div>
</template>

<script>
    import { tagService } from '@/service/tag.service'
    import { loaderMixin } from "@/mixins/loaderMixin";

    export default {
        name: "TagList",
        data() {
            return {
                tags: []
            }
        },
        async created() {
            try {
                this.startLoader()
                const tags = await this.getTags()
                this.tags = tags.sort()
                this.finishLoader()
            } catch (err) {
                console.log(err)
            }
        },
        methods: {
            async getTags() {
                return await this.tagService.getTags()
            },
            navigateArtists(tag) {
                this.$router.push({ name: 'Artists', params: { tag: tag } })
            }
        },
        props: {
            tagService: {
                default: function () {
                    return tagService
                }
            }
        },
        mixins: [loaderMixin]
    }
</script>

<style scoped lang="less">
  .container {
    text-align: center;
  }

  ul {
    text-align: center;
    /*width: 100%;*/
    list-style-type: none;
  }

  li {
    font-size: 250%;
    padding: 10px;
    font-family: 'Metal Mania', sans-serif;
    &:hover {
      cursor: pointer;
      font-size: 400%;
    }
  }
</style>
