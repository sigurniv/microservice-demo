<template lang="html">
  <div class="container">
    <div class="header">
      <router-link to="/" class="link">Tags </router-link>
      /
      <router-link :to="tag" class="link">{{tag}}</router-link>
    </div>
    <div class="images" v-if="!loading">
      <div v-for="(artist) in artists" :key="artist.id" @click="navigateToArtist(artist.name)">
        <div class="image">
          <img :src="`//${artist.image}`"/>
          <div class="caption">
            {{artist.name}}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    import { artistService } from "@/service/artist.service";
    import { loaderMixin } from "@/mixins/loaderMixin";

    export default {
        name: "ArtistList",
        data() {
            return {
                artists: [],
                tag: this.$route.params.tag
            }
        },
        async created() {
            try {
                this.startLoader()
                const tag = this.$route.params.tag;
                if (!tag) {
                    this.$router.push({ name: 'Tags' })
                }

                const artists = await this.getArtists(tag)
                this.artists = artists.filter(function (artist) {
                    return artist.image != "";
                }).sort()

                this.finishLoader()
            } catch (err) {
                console.log(err)
            }
        },
        methods: {
            async getArtists(tag) {
                return await this.artistService.getArtists(tag)
            },
            navigateToArtist(name) {
                this.$router.push({ name: 'Artist', params: { tag: this.$route.params.tag, artist: name } })
            }
        },
        props: {
            artistService: {
                default: function () {
                    return artistService
                }
            }
        },
        mixins: [loaderMixin]
    }
</script>

<style scoped lang="less">
  .container {
    padding-top: 20px;
  }

  .image {
    width: 100%;
    transition: all .2s ease;
    max-width: 400px;
    margin: 5px;
  }

  img {
    transition: all .2s ease;
    opacity: 0.6;
    filter: alpha(opacity=60);
    zoom: 1;
    width: 100%;
    &:hover {
      /*-webkit-filter: grayscale(100%);*/
      /*-moz-filter: grayscale(100%);*/
      /*-o-filter: grayscale(100%);*/
      /*-ms-filter: grayscale(100%);*/
      /*filter: grayscale(100%);*/
      opacity: 1;
      filter: alpha(opacity=100);
      zoom: 1;
      cursor: pointer;
    }
  }

  .images, .header {
    margin: 0 auto;
    width: 70%;
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
    align-content: space-between;
  }

  .header{
    font-size: 150%;
    padding: 0px 5px;
    margin-bottom: 20px;
    font-family: 'Metal Mania', sans-serif;
    text-align: center;

    & .link {
      color: #fff;
      margin-right: 10px;
      &:nth-child(2) {
        margin-left: 10px;
      }
      &:hover{
        text-decoration: underline;
      }
    }
  }

  .caption {
    font-size: 250%;
    padding: 10px;
    font-family: 'Metal Mania', sans-serif;
    text-align: center;
  }

  @media (min-width: 600px) {
    .images, .header {
      width: 60%;
    }
  }

  @media (min-width: 800px) {
    .images, .header {
      width: 83%;
    }
  }

  @media (min-width: 1025px) {
    .images, .header {
      width: 70%;
    }
  }

</style>
