<template>
  <div class="container">
    <div class="artist" v-if="!loading" ref="container">
      <div class="image">
        <img :src="`//${artist.image}`"/>
      </div>

      <div class="caption">
        <div>
          {{artist.name}}
        </div>
        <div class="description">
          {{artist.description.text}}
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
                artist: {}
            }
        },
        async created() {
            try {
                this.startLoader()
                const name = this.$route.params.artist;
                if (!name) {
                    this.$router.push({ name: 'Tags' })
                }

                this.artist = await this.getArtist(name)
                this.finishLoader()
            } catch (err) {
                console.log(err)
            }
        },
        methods: {
            async getArtist(name) {
                return await this.artistService.getArtist(name)
            },
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

<style lang="less" scoped>
  .container {
    padding-top: 20px;
  }

  .artist {
    margin: 0 auto;
    width: 70%;
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
    align-content: space-between;
  }

  .image {
    width: 100%;
    transition: all .2s ease;
    max-width: 400px;
    margin: 5px;
  }

  img {
    width: 100%;
  }

  .caption {
    font-size: 250%;
    font-family: 'Metal Mania', sans-serif;
    text-align: left;
    width: 50%;
    margin: 0 10px 10px;

    & .description {
      font-size: 50%;
      font-family: 'Consolas', monospace;
    }
  }

  @media (min-width: 600px) {
    .artist {
      width: 60%;
    }
  }

  @media (min-width: 800px) {
    .artist {
      width: 83%;
    }
  }

  @media (min-width: 1025px) {
    .artist {
      width: 70%;
    }
  }
</style>
