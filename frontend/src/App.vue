<template>
  <div style="height: 100%;">
    <router-view/>
  </div>
</template>

<style lang="less">
  @import url('//fonts.googleapis.com/css2?family=Metal+Mania&display=swap.css');

  * {
    margin: 0;
    padding: 0;
  }

  .container {
    padding-top: 10px;
    min-height: 100vh;
    width: 100%;
    background: #000;
    color: #fff;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }
</style>
