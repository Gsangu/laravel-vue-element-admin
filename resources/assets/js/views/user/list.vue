<template>
  <div class="user-list">
    <sticky class-name="sub-navbar draft">
      <el-button size="small" type="primary" icon="el-icon-circle-plus-outline" plain @click="addUser">添加用户</el-button>
    </sticky>
    <div class="app-container">
      <el-row>
        <el-col >
          <el-table v-loading.body="listLoading" :data="list" border fit highlight-current-row style="width: 100%">
            <el-table-column align="center" label="序号" width="50">
              <template slot-scope="scope">
                <span>{{ scope.row.id }}</span>
              </template>
            </el-table-column>

            <el-table-column label="昵称" align="center">
              <template slot-scope="scope">
                <span>{{ scope.row.name }}</span>
              </template>
            </el-table-column>

            <el-table-column label="头像" align="center">
              <template slot-scope="scope">
                <span><img :src="siteUrl + scope.row.avatar" width="30"></span>
              </template>
            </el-table-column>

            <el-table-column label="文章" align="center">
              <template slot-scope="scope">
                <span>{{ scope.row.articles.length }}</span>
              </template>
            </el-table-column>

            <el-table-column label="权限" align="center">
              <template slot-scope="scope">
                <span>{{ scope.row.roles.data | getRoles }}</span>
              </template>
            </el-table-column>

            <el-table-column align="center" label="操作" width="100">
              <template slot-scope="scope">
                <el-button type="primary" size="small" circle icon="el-icon-edit" @click="getUser(scope.row.id)"/>
                <el-tooltip :disabled=" id !== scope.row.id && scope.row.articles.length === 0 " effect="dark" content="不能删除自己或用户下文章不为空" placement="top">
                  <span>
                    <el-button :disabled="id === scope.row.id || scope.row.articles.length !== 0" type="danger" size="small" icon="el-icon-delete" style="margin-left:0" circle @click="delUser(scope.row.id)"/>
                  </span>
                </el-tooltip>
              </template>
            </el-table-column>
          </el-table>

          <div class="pagination-container">
            <el-pagination
              :current-page="listQuery.page"
              :page-sizes="[10,20,30, 50]"
              :page-size="listQuery.limit"
              :total="total"
              background
              layout="total, sizes, prev, pager, next, jumper"
              @size-change="handleSizeChange"
              @current-change="handleCurrentChange"/>
          </div>
        </el-col>
      </el-row>
      <el-dialog
        :visible.sync="centerDialogVisible"
        title="用户管理"
        width="30%"
        center>
        <el-form ref="postForm" :model="postForm" :rules="rules">
          <el-form-item label-width="80px" label="名称:" prop="name" class="userInfo-container-item">
            <el-input v-model="postForm.name" />
          </el-form-item>
          <el-form-item label-width="80px" label="邮箱:" prop="email" class="userInfo-container-item">
            <el-input v-model="postForm.email" :disabled="isEdit" type="email" />
          </el-form-item>
          <el-form-item label-width="80px" label="密码:" prop="password" class="userInfo-container-item">
            <el-input v-model="postForm.password" type="password" />
          </el-form-item>
          <el-form-item label-width="80px" label="确认密码:" prop="checkPass" class="userInfo-container-item">
            <el-input v-model="postForm.checkPass" type="password" />
          </el-form-item>
          <el-form-item label-width="80px" label="用户级别:" class="userInfo-container-item">
            <el-select
              v-model="postForm.roles.data"
              multiple
              placeholder="请选择">
              <el-option
                v-for="item in roles"
                :key="item.value"
                :label="item.label"
                :value="item.value ? item.value : ''"/>
            </el-select>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="centerDialogVisible = false">取 消</el-button>
          <el-button :loading="loading" type="primary" @click="editUser">提交</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { fetchList, updateUser, createUser, checkEmail, deleteUser } from '@/api/user'
import Sticky from '@/components/Sticky' // 粘性header组件
import { isvalidUsername } from '@/utils/validate'
import { mapGetters } from 'vuex'

const defaultForm = {
  name: '',
  password: '', // 短链接
  checkPass: '',
  avatar: '',
  id: '',
  roles: { data: ['editor'] },
  email: ''
}
const roles = [{
  value: 'admin',
  label: '管理员'
},
{ value: 'editor',
  label: '普通用户'
}
]

export default {
  name: 'UserList',
  components: { Sticky },
  filters: {
    getRoles: function(val) {
      val = val.toString()
      val = val.replace('admin', '管理员')
      val = val.replace('editor', '普通用户')
      return val
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
    var validatePass = (rule, value, callback) => {
      if (value) {
        console.log(value)
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
    var validateemail = (rule, value, callback) => {
      if (isvalidUsername(value) === false) {
        callback(new Error('请输入正确的Email'))
      } else {
        checkEmail(value).then(response => {
          const data = response.data
          if (data.err) {
            callback(new Error('Email已存在'))
          }
          callback()
        }).catch(err => {
          console.log(err)
        })
      }
    }
    return {
      isEdit: false,
      id: null,
      list: null,
      total: 0,
      loading: false,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10
      },
      roles: roles,
      centerDialogVisible: false,
      postForm: Object.assign({}, defaultForm),
      rules: {
        name: [{ validator: validateRequire }],
        password: [
          { validator: validatePass, trigger: 'blur' }
        ],
        checkPass: [
          { validator: validatePass2 }
        ],
        email: [
          { validator: validateemail, trigger: 'blur' }
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
    this.getList()
    this.id = this.$store.state.user.userid
    this.postForm.id = this.id
  },

  methods: {
    getList() {
      this.listLoading = true
      fetchList(this.listQuery).then(response => {
        this.list = response.data.items
        // console.log(this.list)
        this.total = response.data.total
        this.listLoading = false
      })
    },
    addUser() {
      this.centerDialogVisible = true
      this.isEdit = false
      this.postForm = Object.assign({}, defaultForm)
      if (this.$refs.postForm !== undefined) {
        this.$refs.postForm.resetFields()
      }
    },
    getUser(val) {
      const id = val - (this.listQuery.page - 1) * this.listQuery.limit - 1
      this.postForm = this.list[id]
      this.isEdit = true
      this.centerDialogVisible = true
    },
    editUser() {
      this.loading = true
      if (this.isEdit === true) {
        updateUser(this.postForm).then(response => {
          this.loading = false
          const data = response.data
          if (data.err) {
            this.$message({
              message: data.err,
              type: 'error'
            })
          } else if (data.logout && this.postForm.id === this.$store.state.user.userid) {
            this.$store.dispatch('LogOut').then(() => {
              this.$message({
                message: data.logout,
                showClose: true,
                type: 'warning',
                onClose: function() {
                  location.reload()
                }
              })
            })
          } else {
            this.centerDialogVisible = false
            this.getList()
            this.$notify({
              title: '成功',
              message: '修改成功',
              type: 'success',
              duration: 2000
            })
          }
        }).catch(err => {
          console.log(err)
        })
      } else {
        createUser(this.postForm).then(response => {
          const data = response.data
          this.loading = false
          if (data.err) {
            this.$message({
              message: data.err,
              type: 'error'
            })
          } else {
            this.centerDialogVisible = false
            this.getList()
            this.$notify({
              title: '成功',
              message: '修改成功',
              type: 'success',
              duration: 2000
            })
          }
        }).catch(err => {
          console.log(err)
        })
      }
    },
    delUser(id) {
      this.$confirm('此操作将永久删除该用户, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        deleteUser(id).then(response => {
          if (response.data.err) {
            this.$message({
              message: response.data.err,
              type: 'error'
            })
          } else {
            this.getList()
            this.$notify({
              title: '成功',
              message: '删除分类成功',
              type: 'success',
              duration: 2000
            })
          }
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        })
      })
    },
    handleSizeChange(val) {
      this.listQuery.limit = val
      this.getList()
    },
    handleCurrentChange(val) {
      this.listQuery.page = val
      this.getList()
    }
  }
}
</script>
