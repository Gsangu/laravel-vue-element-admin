<template>
  <div class="category-list">
    <sticky class-name="sub-navbar draft">
      <el-button size="small" type="primary" icon="el-icon-circle-plus-outline" plain @click="addCategory">添加栏目</el-button>
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

            <el-table-column align="center" label="日期" width="200">
              <template slot-scope="scope">
                <span>{{ scope.row.created_at | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
              </template>
            </el-table-column>

            <el-table-column label="名称">
              <template slot-scope="scope">
                <span>{{ scope.row.name }}</span>
              </template>
            </el-table-column>

            <el-table-column label="短链接" >
              <template slot-scope="scope">
                <span>{{ scope.row.slug }}</span>
              </template>
            </el-table-column>

            <el-table-column label="文章数量" >
              <template slot-scope="scope">
                <span>{{ scope.row.articles ? scope.row.articles.length:0 }}</span>
              </template>
            </el-table-column>

            <el-table-column align="center" label="操作" width="100">
              <template slot-scope="scope">
                <el-button :disabled="scope.row.id < 6" type="primary" size="small" circle icon="el-icon-edit" @click="getCategory(scope.row.id)"/>
                <el-tooltip :disabled=" !scope.row.articles " effect="dark" content="系统分类不能删除或该分类不为空！" placement="top">
                  <span>
                    <el-button :disabled="scope.row.id < 6 || scope.row.articles" type="danger" size="small" icon="el-icon-delete" style="margin-left:0" circle @click="delCategory(scope.row.id)"/>
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
        title="栏目管理"
        width="30%"
        center>
        <el-form ref="postForm" :model="postForm" :rules="rules">
          <el-form-item label-width="80px" label="名称:" prop="name" class="categoryInfo-container-item">
            <el-input v-model="postForm.name" placeholder="请输入栏目名称"/>
          </el-form-item>
          <el-form-item label-width="80px" label="短链接:" prop="slug" class="categoryInfo-container-item">
            <el-input v-model="postForm.slug" placeholder="请输入短链接"/>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="centerDialogVisible = false">取 消</el-button>
          <el-button :loading="loading" type="primary" @click="editCategory">提交</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { checkSlug, fetchList, deleteCategory, createCategory, updateCategory } from '@/api/category'
import Sticky from '@/components/Sticky' // 粘性header组件

const defaultForm = {
  name: '',
  slug: '' // 短链接
}

export default {
  name: 'CategoryList',
  components: { Sticky },
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
      if (this.isEdit === true) { callback() }
      if (value) {
        checkSlug(value).then(response => {
          const data = response.data
          if (data.err) {
            return callback(new Error(data.err))
          } else {
            callback()
          }
        }).catch(err => {
          console.log(err)
          callback(new Error('服务错误'))
        })
      } else {
        callback(new Error('slug不能为空'))
      }
    }
    return {
      isEdit: false,
      list: null,
      total: 0,
      loading: false,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10
      },
      centerDialogVisible: false,
      postForm: Object.assign({}, defaultForm),
      rules: {
        name: [{ validator: validateRequire }],
        // content: [{ validator: validateRequire, trigger: 'blur' }],
        slug: [{ validator: validateSlug, trigger: 'blur' }]
      }
    }
  },
  created() {
    this.getList()
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
    handleSizeChange(val) {
      this.listQuery.limit = val
      this.getList()
    },
    handleCurrentChange(val) {
      this.listQuery.page = val
      this.getList()
    },
    getCategory(val) {
      const id = val - (this.listQuery.page - 1) * this.listQuery.limit - 1
      this.postForm = this.list[id]
      this.isEdit = true
      this.centerDialogVisible = true
    },
    addCategory() {
      this.centerDialogVisible = true
      this.isEdit = false
      this.postForm = Object.assign({}, defaultForm)
      if (this.$refs.postForm !== undefined) {
        this.$refs.postForm.resetFields()
      }
    },
    editCategory() {
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.loading = true
          if (this.isEdit === true) {
            updateCategory(this.postForm).then(response => {
              this.loading = false
              if (response.data.err) {
                this.$message({
                  message: response.data.err,
                  type: 'error'
                })
              } else {
                this.getList()
                this.centerDialogVisible = false
                this.$notify({
                  title: '成功',
                  message: '分类修改成功',
                  type: 'success',
                  duration: 2000
                })
              }
            }).catch(err => {
              console.log(err)
            })
          } else {
            createCategory(this.postForm).then(response => {
              this.loading = false
              if (response.data.err) {
                this.$message({
                  message: response.data.err,
                  type: 'error'
                })
              } else {
                this.centerDialogVisible = false
                this.getList()
                this.$notify({
                  title: '成功',
                  message: '创建分类成功',
                  type: 'success',
                  duration: 2000
                })
              }
            }).catch(err => {
              console.log(err)
            })
          }
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    delCategory(id) {
      this.$confirm('此操作将永久删除该分类, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        deleteCategory(id).then(response => {
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
    }
  }
}
</script>
