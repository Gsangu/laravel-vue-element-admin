<template>
  <div class="components-container">
    <el-row :span="24">
      <el-col :span="16" :offset="4">
        <el-card shadow="always">
          <el-row :span="24">
            <el-col :span="12" :offset="6">
              <el-form ref="postForm" :model="postForm" :rules="rules" label-width="80px" >
                <el-form-item label="头像">
                  <el-row :span="24">
                    <el-col :span="12" :offset="7">
                      <pan-thumb v-model="postForm.avatar" :image="siteUrl + postForm.avatar" width="80px" height="80px" @click="imagecropperShow=true"/>
                    </el-col>
                  </el-row>
                  <image-cropper
                    v-show="imagecropperShow"
                    :width="60"
                    :height="60"
                    :key="imagecropperKey"
                    :params="params"
                    url="/auth/uploadFile"
                    field="file"
                    lang-type="zh"
                    @close="close"
                    @crop-upload-success="cropSuccess"/>
                </el-form-item>
                <el-form-item label="昵称:">
                  <el-input v-model="postForm.name"/>
                </el-form-item>
                <el-form-item label="密码:" prop="password">
                  <el-input v-model="postForm.password" type="password" autocomplete="off"/>
                </el-form-item>
                <el-form-item label="确认密码:" prop="checkPass">
                  <el-input v-model="postForm.checkPass" type="password" autocomplete="off"/>
                </el-form-item>
                <el-form-item size="large">
                  <el-button type="primary" @click="onSubmit">保存修改</el-button>
                  <el-button @click="resetForm('postForm')">取消修改</el-button>
                </el-form-item>
              </el-form>
            </el-col>
          </el-row>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import ImageCropper from '@/components/ImageCropper'
import PanThumb from '@/components/PanThumb'
import { updateUser } from '@/api/user'
import { mapGetters } from 'vuex'

const defaultForm = {
  name: '',
  avatar: '',
  id: '',
  password: '',
  checkPass: ''
}

export default {
  name: 'UserInfo',
  components: { ImageCropper, PanThumb },
  data() {
    var validatePass = (rule, value, callback) => {
      if (value !== '') {
        if (value.length < 6) {
          callback(new Error('密码至少要6位'))
        }
        this.$refs.postForm.validateField('checkPass')
      }
      callback()
    }
    var validatePass2 = (rule, value, callback) => {
      if (value !== this.postForm.password && this.postForm.password !== '') {
        callback(new Error('两次输入密码不一致!'))
      } else {
        callback()
      }
    }
    return {
      postForm: Object.assign({}, defaultForm),
      imagecropperShow: false,
      imagecropperKey: 0,
      params: {
        avatar: 'avatar'
      },
      rules: {
        password: [
          { validator: validatePass, trigger: 'blur' }
        ],
        checkPass: [
          { validator: validatePass2 }
        ]
      }
    }
  },
  computed: {
    ...mapGetters([
      'siteUrl'
    ])
  },
  created() {
    this.postForm.name = this.$store.state.user.name
    this.postForm.id = this.$store.state.user.userid
    this.postForm.avatar = this.$store.state.user.avatar
  },
  methods: {
    cropSuccess(resData) {
      this.imagecropperShow = false
      this.imagecropperKey = this.imagecropperKey + 1
      this.postForm.avatar = resData.imgUrl
    },
    close() {
      this.imagecropperShow = false
    },
    onSubmit() {
      this.$refs.postForm.validate((valid) => {
        if (valid) {
          updateUser(this.postForm).then(response => {
            const data = response.data
            if (data.err) {
              this.$message({
                message: data.err,
                type: 'error'
              })
            } else if (data.logout) {
              this.$store.dispatch('LogOut').then(() => {
                this.$message({
                  message: data.logout,
                  type: 'error',
                  duration: 2000,
                  onClose: function() {
                    location.reload()
                  }
                })
              })
            } else {
              this.$notify({
                title: '成功',
                message: '修改成功',
                type: 'success',
                duration: 2000,
                onClose: function() {
                  location.reload()
                }
              })
            }
          }).catch(err => {
            console.log(err)
          })
        }
      })
    },
    resetForm(formName) {
      this.$refs[formName].resetFields()
    }
  }
}
</script>

<style scoped>
  .avatar{
    width: 200px;
    height: 200px;
    border-radius: 50%;
  }
</style>
