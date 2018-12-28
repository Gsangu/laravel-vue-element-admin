<template>
  <div class="message-list">
    <sticky class-name="sub-navbar draft">
      <el-button size="small" type="primary" icon="el-icon-circle-plus-outline" plain @click="addField">添加字段</el-button>
    </sticky>
    <div class="app-container">
      <el-row :gutter="24"/>
      <el-table v-loading.body="listLoading" ref="fieldsTable" :data="list" border fit highlight-current-row style="width: 100%">
        <el-table-column align="center" label="序号" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>

        <el-table-column align="center" label="名称">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>

        <el-table-column align="center" label="索引">
          <template slot-scope="scope">
            <span>{{ scope.row.index }}</span>
          </template>
        </el-table-column>

        <el-table-column align="center" label="操作" width="100">
          <template slot-scope="scope">
            <el-button :disabled="scope.row.id < 7" circle type="primary" size="small" icon="el-icon-edit" @click="getField(scope.row.id)"/>
            <el-button :disabled="scope.row.id < 7" circle type="danger" size="small" icon="el-icon-delete" @click="deleteField(scope.row.id)"/>
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
      <el-dialog
        :visible.sync="centerDialogVisible"
        title="字段管理"
        width="30%"
        center>
        <el-form ref="postForm" :model="postForm" :rules="rules">
          <el-form-item label-width="80px" label="名称:" prop="name" class="fieldInfo-container-item">
            <el-input v-model="postForm.name" placeholder="请输入字段名称"/>
          </el-form-item>
          <el-form-item label-width="80px" label="索引:" prop="index" class="fieldInfo-container-item">
            <el-input v-model="postForm.index" placeholder="请输入字段索引"/>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="centerDialogVisible = false">取 消</el-button>
          <el-button :loading="loading" type="primary" @click="editField">提交</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { fetchList, updateField, createField, deleteField, checkIndex } from '@/api/field'
import Sticky from '@/components/Sticky' // 粘性header组件

const defaultForm = {
  name: '',
  index: ''
}

export default {
  name: 'FieldList',
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
    const validateIndex = (rule, value, callback) => {
      if (this.isEdit === true) { callback() }
      if (value) {
        checkIndex(value).then(response => {
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
        callback(new Error('索引不能为空'))
      }
    }
    return {
      isEdit: false,
      list: null,
      total: 0,
      loading: false,
      listLoading: true,
      multipleSelection: [],
      listQuery: {
        page: 1,
        limit: 10
      },
      fields: [],
      roles: [],
      new_status: null,
      centerDialogVisible: null,
      postForm: Object.assign({}, defaultForm),
      rules: {
        name: [{ validator: validateRequire }],
        // content: [{ validator: validateRequire, trigger: 'blur' }],
        index: [{ validator: validateIndex, trigger: 'blur' }]
      }
    }
  },
  created() {
    this.getList()
    this.roles = this.$store.state.user.roles
  },
  methods: {
    toggleSelection() {
      this.$refs.messagesTable.clearSelection()
    },
    getField(val) {
      const id = val - (this.listQuery.page - 1) * this.listQuery.limit - 1
      this.postForm = this.list[id]
      this.isEdit = true
      this.centerDialogVisible = true
    },
    editField() {
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.loading = true
          console.log(this.postForm)
          if (this.isEdit === true) {
            updateField(this.postForm).then(response => {
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
                  message: '字段修改成功',
                  type: 'success',
                  duration: 2000
                })
              }
            }).catch(err => {
              console.log(err)
            })
          } else {
            createField(this.postForm).then(response => {
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
                  message: '创建字段成功',
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
    addField() {
      this.centerDialogVisible = true
      this.isEdit = false
      this.postForm = Object.assign({}, defaultForm)
      if (this.$refs.postForm !== undefined) {
        this.$refs.postForm.resetFields()
      }
    },
    deleteSelection() {
      this.$confirm('此操作将永久这些删除消息, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const ids = this.multipleSelection.map(v => v.id)
        deleteField(ids).then(response => {
          if (response.data.err) {
            this.$message({
              message: response.data.err,
              type: 'error'
            })
          } else {
            this.getList()
            this.$notify({
              title: '成功',
              type: 'success',
              duration: 2000,
              message: '删除成功!'
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
    tableRowClassName({ row, rowIndex }) {
      if (!row.status) {
        return 'success-row'
      }
      return ''
    },
    getFieldName(val) {
      return this.fields[val]
    },
    delField(id) {
      this.$confirm('此操作将永久删除该分类, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        deleteField(id).then(response => {
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
    getList() {
      this.listLoading = true
      fetchList(this.listQuery).then(response => {
        this.list = response.data.items
        this.fields = response.data.fields
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
    handleSelectionChange(val) {
      this.multipleSelection = val
      this.checkSelection()
    },
    checkSelection() {
      if (this.multipleSelection.length === 0) {
        return false
      }
      return true
    }
  }
}
</script>

<style>

.el-table .success-row {
  background:#f0f9eb;
}

.demo-table-expand {
    font-size: 0;
}
.demo-table-expand label {
    width: 90px;
    color: #99a9bf;
}
.demo-table-expand .el-form-item {
    margin-right: 0;
    margin-bottom: 0;
    width: 50%;
}
</style>
