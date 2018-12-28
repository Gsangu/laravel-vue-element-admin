<template>
  <div class="message-list">
    <sticky class-name="sub-navbar draft">
      <el-button :disabled="!checkSelection() && roles.includes('admin')" size="small" style="margin-left:10px" type="danger" icon="el-icon-delete" plain @click="deleteSelection">批量删除</el-button>
      <el-button :disabled="!checkSelection()" size="small" type="warning" icon="el-icon-edit" plain @click="editSelection">批量审核</el-button>
      <el-button :disabled="!checkSelection()" size="small" type="info" icon="el-icon-close" plain @click="toggleSelection">取消选择</el-button>
    </sticky>
    <div class="app-container">
      <el-row>
        <el-col >
          <el-table v-loading.body="listLoading" ref="messagesTable" :row-class-name="tableRowClassName" :data="list" border fit highlight-current-row style="width: 100%" @selection-change="handleSelectionChange">

            <el-table-column type="expand">
              <template slot-scope="props">
                <el-form label-position="left" inline class="demo-table-expand">
                  <el-form-item v-for="(item,index) in props.row.content" :key="item+index" :label="getFieldName(index)">
                    <span>{{ item }}</span>
                  </el-form-item>
                </el-form>
              </template>
            </el-table-column>
            <el-table-column
              type="selection"
              width="55"/>
            <el-table-column
              align="center"
              width="55"
              label="ID"
              prop="id"/>
            <el-table-column
              align="center"
              width="150"
              label="日期"
              prop="created_at"/>
            <el-table-column
              align="center"
              label="内容"
              prop="content.content"/>
            <el-table-column
              align="center"
              label="来源网址"
              prop="sourceUrl"/>
            <el-table-column align="center" width="150" label="IP">
              <template slot-scope="scope">
                <span><a :href="'http://ip-api.com/#' + scope.row.ip" target="_blank" style="text-decoration:underline">{{ scope.row.ip }}</a></span>
              </template>
            </el-table-column>
            <el-table-column
              width="100"
              align="center"
              label="系统"
              prop="system"/>
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
    </div>
  </div>
</template>

<script>
import { fetchList, deleteMessage, batchUpdateMessage } from '@/api/message'
import Sticky from '@/components/Sticky' // 粘性header组件

export default {
  name: 'MessageList',
  components: { Sticky },
  data() {
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
      new_status: null
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
    deleteSelection() {
      this.$confirm('此操作将永久这些删除消息, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const ids = this.multipleSelection.map(v => v.id)
        deleteMessage(ids).then(response => {
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
    editSelection() {
      this.$confirm('确定已审核, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const ids = this.multipleSelection.map(v => v.id)
        this.new_status = true
        batchUpdateMessage(ids, this.new_status).then(response => {
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
              type: 'success',
              duration: 2000,
              message: '修改成功!'
            })
          }
        }).catch(err => {
          console.log(err)
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
