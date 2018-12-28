<template>
  <div class="createPost-container">
    <el-form ref="postForm" :model="postForm" :rules="rules" class="form-container">

      <sticky :class-name="'sub-navbar '+sticky">
        <!--<CommentDropdown v-model="postForm.comment_disabled" />
        <PlatformDropdown v-model="postForm.platforms" />-->
        <StatusDropdown v-model="postForm.status" />
        <SlugDropdown v-model="postForm.slug" />
        <el-button v-loading="loading" style="margin-left: 10px;" type="success" @click="submitForm">提交
        </el-button>
      </sticky>

      <div class="createPost-main-container">
        <el-row>

          <el-col :span="24">
            <el-form-item style="margin-bottom: 40px;" prop="title">
              <MDinput v-model="postForm.title" :maxlength="100" name="name" required>
                标题
              </MDinput>
            </el-form-item>

            <div class="postInfo-container">
              <el-row>
                <el-col :span="8">
                  <el-form-item label-width="45px" label="作者:" class="postInfo-container-item">
                    <el-select v-model="postForm.user_id" placeholder="选择用户">
                      <el-option v-for="(item,index) in users" :key="item+index" :label="item" :value="index"/>
                    </el-select>
                  </el-form-item>
                </el-col>

                <el-col :span="10">
                  <el-form-item label-width="80px" label="发布时间:" class="postInfo-container-item">
                    <el-date-picker v-model="postForm.published_at" type="datetime" format="yyyy-MM-dd HH:mm:ss" placeholder="选择日期时间"/>
                  </el-form-item>
                </el-col>

                <el-col :span="6">
                  <el-form-item label-width="45px" label="栏目:" class="postInfo-container-item">
                    <el-select v-model="postForm.category_id" placeholder="选择栏目">
                      <el-option v-for="(item,index) in categories" :key="item+index" :label="item" :value="index"/>
                    </el-select>
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
          </el-col>
        </el-row>

        <el-form-item style="margin-bottom: 40px;" label-width="45px" label="摘要:">
          <el-input :rows="1" v-model="postForm.content_short" type="textarea" class="article-textarea" autosize placeholder="请输入内容"/>
          <span v-show="contentShortLength" class="word-counter">{{ contentShortLength }}字</span>
        </el-form-item>

        <div class="editor-container">
          <Tinymce ref="editor" :height="400" v-model="postForm.content" />
        </div>

        <div style="margin-bottom: 20px;">
          <Upload v-model="postForm.image_uri" />
        </div>
      </div>
    </el-form>

  </div>
</template>

<script>
import Tinymce from '@/components/Tinymce'
import Upload from '@/components/Upload/singleImage'
import MDinput from '@/components/MDinput'
import Sticky from '@/components/Sticky' // 粘性header组件
import { fetchArticle, checkSlug, createArticle, updateArticle } from '@/api/article'
import { userSearch } from '@/api/remoteSearch'
import { SlugDropdown, StatusDropdown } from './Dropdown'
import { mapGetters } from 'vuex'

const defaultForm = {
  status: 0,
  title: '', // 文章题目
  user_id: '',
  content: '', // 文章内容
  content_short: '', // 文章摘要
  slug: '', // 文章短链接
  image_uri: '', // 文章图片
  published_at: '', // 发布时间
  id: '',
  category_id: ''
}

export default {
  name: 'ArticleDetail',
  components: { Tinymce, MDinput, Upload, Sticky, SlugDropdown, StatusDropdown },
  props: {
    isEdit: {
      type: Boolean,
      default: false
    }
  },
  data() {
    const validateRequire = (rule, value, callback) => {
      if (value === '') {
        this.$message({
          message: rule.field + '不能为空',
          type: 'error'
        })
        callback(new Error(rule.field + '不能为空'))
      } else {
        callback()
      }
    }
    const validateSlug = (rule, value, callback) => {
      if (value) {
        checkSlug(value).then(response => {
          const data = response.data
          if (data.err) {
            this.$message({
              message: data.err,
              type: 'error'
            })
            callback(new Error(data.err))
          } else {
            callback()
          }
        }).catch(err => {
          console.log(err)
          callback(new Error('服务错误'))
        })
      } else {
        this.$message({
          message: 'slug不能为空',
          type: 'error'
        })
        callback(new Error('slug不能为空'))
      }
    }

    return {
      postForm: Object.assign({}, defaultForm),
      sticky: 'draft',
      loading: false,
      userListOptions: [],
      temp_slug: '',
      rules: {
        title: [{ validator: validateRequire }],
        slug: [{ validator: validateSlug, trigger: 'blur' }]
      }
    }
  },
  computed: {
    ...mapGetters([
      'categories',
      'users',
      'siteUrl'
    ]),
    contentShortLength() {
      return this.postForm.content_short.length
    }
  },
  created() {
    if (this.isEdit) {
      const id = this.$route.params && this.$route.params.id
      this.rules.slug = false
      this.fetchData(id)
    } else {
      this.postForm = Object.assign({}, defaultForm)
    }
    if (this.$store.state.category.list.length === 0) { this.getCategoryList() }
    if (this.$store.state.user.list.length === 0) { this.getUserList() }
  },
  methods: {
    getCategoryList() {
      this.$store.dispatch('GetCategoryList')
    },
    getUserList() {
      this.$store.dispatch('GetUserList')
    },
    fetchData(id) {
      fetchArticle(id).then(response => {
        const item = response.data
        this.postForm = item
        this.sticky = item.status ? 'published' : 'draft'
      }).catch(err => {
        console.log(err)
      })
    },
    submitForm() {
      this.$refs.postForm.validate(valid => {
        if (valid) {
          if (this.postForm.content === '') {
            this.$message({
              message: 'content不能为空',
              type: 'error'
            })
            return
          }
          this.loading = true
          if (this.isEdit === true) {
            updateArticle(this.postForm).then(response => {
              if (response.data.err) {
                this.$message({
                  message: response.data.err,
                  type: 'error'
                })
              } else {
                this.$notify({
                  title: '成功',
                  message: '文章修改成功',
                  type: 'success',
                  duration: 2000
                })
              }
            }).catch(err => {
              console.log(err)
            })
          } else {
            createArticle(this.postForm).then(response => {
              if (response.data.err) {
                this.$message({
                  message: response.data.err,
                  type: 'error'
                })
              } else {
                this.$notify({
                  title: '成功',
                  message: '发布文章成功',
                  type: 'success',
                  duration: 2000
                })
                this.postForm.sticky = 'published'
              }
            }).catch(err => {
              console.log(err)
            })
          }
          this.loading = false
          this.$router.push({ path: this.redirect || '/article/list' })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    getRemoteUserList(query) {
      userSearch(query).then(response => {
        if (!response.data) return
        this.userListOptions = response.data.map(v => v.name)
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
@import "resources/assets/js/styles/mixin.scss";
.createPost-container {
  position: relative;
  .createPost-main-container {
    padding: 40px 45px 20px 50px;
    .postInfo-container {
      position: relative;
      @include clearfix;
      margin-bottom: 10px;
      .postInfo-container-item {
        float: left;
      }
    }
    .editor-container {
      min-height: 500px;
      margin: 0 0 30px;
      .editor-upload-btn-container {
        text-align: right;
        margin-right: 10px;
        .editor-upload-btn {
          display: inline-block;
        }
      }
    }
  }
  .word-counter {
    width: 40px;
    position: absolute;
    right: -10px;
    top: 0px;
  }
}
</style>
