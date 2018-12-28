<template>
  <div class="createCategory-container">
    <el-form ref="postForm" :model="postForm" :rules="rules" class="form-container">
      <sticky :class-name="'sub-navbar '+postForm.sticky">
        <el-button type="success" @click="submitForm">提交</el-button>
        <el-button @click="resetForm">重置</el-button>
      </sticky>

      <div class="createCategory-main-container">
        <el-col :span="24">
          <div class="categoryInfo-container">
            <el-col :span="24">
              <el-form-item style="margin-bottom: 40px;" prop="name">
                <MDinput v-model="postForm.name" :maxlength="100" name="name" required>
                  分类名
                </MDinput>
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item style="margin-bottom: 40px;" prop="slug">
                <MDinput v-model="postForm.slug" :maxlength="100" name="slug">
                  短链接
                </MDinput>
              </el-form-item>
            </el-col>
          </div>
        </el-col>
      </div>
    </el-form>

  </div>
</template>

<script>
import { checkSlug, fetchCategory, createCategory, updateCategory } from '@/api/category'
import MDinput from '@/components/MDinput'
import Sticky from '@/components/Sticky' // 粘性header组件

const defaultForm = {
  name: '',
  slug: '', // 短链接
  sticky: 'draft'
}

export default {
  name: 'CategoryDetail',
  components: { MDinput, Sticky },
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
            callback(new Error(data.msg))
          }
        }).catch(err => {
          console.log(err)
          callback(new Error('服务错误'))
        })
        callback()
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
      loading: false,
      userListOptions: [],
      temp_slug: '',
      rules: {
        name: [{ validator: validateRequire }],
        // content: [{ validator: validateRequire, trigger: 'blur' }],
        slug: [{ validator: validateSlug }]
      }
    }
  },
  created() {
    if (this.isEdit) {
      const id = this.$route.params && this.$route.params.id
      this.fetchData(id)
    } else {
      this.postForm = Object.assign({}, defaultForm)
    }
  },
  methods: {
    fetchData(id) {
      fetchCategory(id).then(response => {
        this.postForm = response.data.item
        this.postForm.sticky = response.data.item.status
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
            updateCategory(this.postForm).then(response => {
              if (response.data.err) {
                this.$message({
                  message: response.data.err,
                  type: 'error'
                })
                return
              }
              this.$notify({
                title: '成功',
                message: '分类修改成功',
                type: 'success',
                duration: 2000
              })
              this.$store.dispatch('GetCategoryList')
            }).catch(err => {
              console.log(err)
            })
          } else {
            createCategory(this.postForm).then(response => {
              if (response.data.err) {
                this.$message({
                  message: response.data.err,
                  type: 'error'
                })
                return
              }
              this.$notify({
                title: '成功',
                message: '创建分类成功',
                type: 'success',
                duration: 2000
              })
              this.$store.dispatch('GetCategoryList')
              this.postForm.sticky = 'published'
            }).catch(err => {
              console.log(err)
            })
          }
          this.loading = false
          this.$router.push({ path: this.redirect || '/category/list' })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
@import "resources/assets/js/styles/mixin.scss";
.createCategory-container {
  position: relative;
  .createCategory-main-container {
    padding: 40px 45px 20px 50px;
    .categoryInfo-container {
      position: relative;
      @include clearfix;
      margin-bottom: 10px;
      .categoryInfo-container-item {
        float: left;
      }
    }
  }
}
</style>
