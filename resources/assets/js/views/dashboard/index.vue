<template>
  <div class="dashboard-editor-container">
    <el-row :gutter="8">
      <el-col :xs="{span: 24}" :sm="{span: 12}" :md="{span: 12}" :lg="{span: 6, offset:1}" :xl="{span: 6, offset:1}" style="margin-bottom:30px;">
        <box-card/>
      </el-col>
      <el-col :xs="{span: 24}" :sm="{span: 24}" :md="{span: 24}" :lg="{span: 12, offset:3}" :xl="{span: 12, offset:3}" style="margin-bottom:30px;">
        <img :src="emptyGif" class="emptyGif">
      </el-col>
    </el-row>

  </div>
</template>

<script>
import TodoList from './components/TodoList'
import BoxCard from './components/BoxCard'

export default {
  name: 'DashboardAdmin',
  components: {
    TodoList,
    BoxCard
  },
  data() {
    return {
      emptyGif: '/uploads/images/default.gif'
    }
  },
  created() {
    if (this.$store.state.user.defaultPass) {
      this.$notify({
        message: '您正在使用默认密码，请及时修改密码！',
        type: 'warning',
        duration: 2000,
        onClose: function() {
          window.location.href = '/admin/#/user/info'
        }
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .emptyGif {
    display: block;
    width: 100%;
    margin: 0 auto;
  }
.dashboard-editor-container {
  min-height: 100vh;
  padding: 50px 60px 0px;
  background-color: #e3e3e3;
}
</style>
