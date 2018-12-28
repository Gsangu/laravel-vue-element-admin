<template>
  <el-card class="box-card-component" style="margin-left:8px;">
    <div slot="header" class="box-card-header">
      <img src="/uploads/images/default.png">
    </div>
    <div style="position:relative;">
      <pan-thumb :image="siteUrl + avatar" class="panThumb"/>
      <div style="padding-top:40px;" class="progress-item">
        <p class="warn-content">
          欢迎{{ name }}登陆！您是{{ roles | statusFilter }}，可以进行如下快捷操作：
        </p>
        <el-row :span="24">
          <el-col :span="7">
            <router-link to="/article/create">
              <el-button style="width:100%" type="primary" plain >添加文章</el-button>
            </router-link>
          </el-col>
          <el-col :span="7" :offset="1">
            <router-link to="/user/info">
              <el-button style="width:100%" type="success" plain >个人中心</el-button>
            </router-link>
          </el-col>
          <el-col :span="7" :offset="1">
            <router-link to="/message/list">
              <el-button style="width:100%" type="info" plain >留言列表</el-button>
            </router-link>
          </el-col>
        </el-row>
      </div>
    </div>
  </el-card>
</template>

<script>
import { mapGetters } from 'vuex'
import PanThumb from './PanThumb'

export default {
  components: { PanThumb },

  filters: {
    statusFilter(roles) {
      const statusMap = {
        admin: '管理员',
        editor: '普通用户'
      }
      return statusMap[roles]
    }
  },
  data() {
    return {
      statisticsData: {
        article_count: 1024,
        pageviews_count: 1024
      }
    }
  },
  computed: {
    ...mapGetters([
      'name',
      'avatar',
      'roles',
      'siteUrl'
    ])
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" >
.box-card-component{
  .el-card__header {
    padding: 0px!important;
  }
}
</style>
<style rel="stylesheet/scss" lang="scss" scoped>
.box-card-component {
  .box-card-header {
    position: relative;
    height: 220px;
    img {
      width: 100%;
      height: 100%;
      transition: all 0.2s linear;
      &:hover {
        transform: scale(1.1, 1.1);
        filter: contrast(130%);
      }
    }
  }
  .panThumb {
    z-index: 100;
    height: 70px!important;
    width: 70px!important;
    position: absolute!important;
    top: -45px;
    left: 0px;
    border: 5px solid #ffffff;
    background-color: #fff;
    margin: auto;
    box-shadow: none!important;
    /deep/ .pan-info {
      box-shadow: none!important;
    }
  }
  .progress-item {
    margin-bottom: 10px;
    font-size: 14px;
  }
}
</style>
