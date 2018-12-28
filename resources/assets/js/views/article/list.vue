<template>
  <div class="article-list">
    <sticky class-name="sub-navbar draft">
      <router-link to="/article/create">
        <el-button type="primary" plain="" size="small" icon="el-icon-circle-plus-outline">添加文章</el-button>
      </router-link>
      <el-button :disabled="!checkSelection()" size="small" style="margin-left:10px" type="danger" icon="el-icon-delete" plain @click="deleteSelection">批量删除</el-button>
      <el-button :disabled="!checkSelection()" size="small" type="warning" icon="el-icon-edit" plain @click="selection">批量修改</el-button>
      <el-button :disabled="!checkSelection()" size="small" type="info" icon="el-icon-close" plain @click="toggleSelection">取消选择</el-button>
    </sticky>
    <div class="app-container">
      <el-row :gutter="24"/>
      <el-table v-loading.body="listLoading" ref="articlesTable" :data="list" border fit highlight-current-row style="width: 100%" @selection-change="handleSelectionChange">
        <el-table-column type="selection" width="40"/>
        <el-table-column align="center" label="序号" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>

        <el-table-column width="180px" align="center" label="日期">
          <template slot-scope="scope">
            <span>{{ scope.row.published_at | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
          </template>
        </el-table-column>

        <el-table-column width="120px" align="center" label="用户">
          <template slot-scope="scope">
            <span>{{ scope.row.user.name }}</span>
          </template>
        </el-table-column>

        <el-table-column width="100px" align="center" label="分类">
          <template slot-scope="scope">
            <span>{{ scope.row.category.name }}</span>
          </template>
        </el-table-column>

        <el-table-column class-name="status-col" label="状态" width="110">
          <template slot-scope="scope">
            <el-tag :type="scope.row.status | statusFilter">{{ scope.row.status ? '已发布' : '草稿' }}</el-tag>
          </template>
        </el-table-column>

        <el-table-column min-width="300px" align="center" label="标题">
          <template slot-scope="scope">

            <router-link :to="'/article/edit/'+scope.row.id" class="link-type">
              <span>{{ scope.row.title }}</span>
            </router-link>
          </template>
        </el-table-column>

        <el-table-column align="center" label="操作" width="100">
          <template slot-scope="scope">
            <router-link :to="'/article/edit/'+scope.row.id">
              <el-button circle type="primary" size="small" icon="el-icon-edit"/>
            </router-link>
            <el-button circle type="danger" size="small" icon="el-icon-delete" @click="deleteArticle(scope.row.id)"/>
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
        title="请选择要进行的操作"
        width="30%"
        center>
        <el-form ref="form" :model="form">
          <el-form-item label-width="45px" label="栏目:" class="postInfo-container-item">
            <el-select v-model="category" placeholder="选择栏目">
              <el-option v-for="(item,index) in categories" :key="item+index" :label="item" :value="index"/>
            </el-select>
          </el-form-item>
          <el-form-item label-width="45px" label="状态:" class="postInfo-container-item">
            <el-select v-model="new_status" placeholder="选择状态">
              <el-option v-for="(item,index) in status_map" :key="item+index" :label="item" :value="index"/>
            </el-select>
          </el-form-item>
          <el-form-item label-width="45px" label="用户:" class="postInfo-container-item">
            <el-select v-model="user" placeholder="选择用户">
              <el-option v-for="(item,index) in users" :key="item+index" :label="item" :value="index"/>
            </el-select>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="centerDialogVisible = false">取 消</el-button>
          <el-button type="primary" @click="editSelection">确 定</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { fetchList, deleteArticle, batchUpdateArticle } from '@/api/article'
import { mapGetters } from 'vuex'
import Sticky from '@/components/Sticky' // 粘性header组件

export default {
  name: 'ArticleList',
  components: { Sticky },
  filters: {
    statusFilter(status) {
      return status ? 'success' : 'info'
    }
  },
  data() {
    return {
      list: null,
      total: 0,
      loading: false,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10
      },
      form: {},
      multipleSelection: [],
      centerDialogVisible: false,
      category: '',
      user: '',
      new_status: '',
      status_map: ['草稿', '已发布']
    }
  },
  computed: {
    ...mapGetters([
      'categories',
      'users'
    ])
  },
  created() {
    this.getList()
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
    getList() {
      this.listLoading = true
      fetchList(this.listQuery).then(response => {
        this.list = response.data.items
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
    deleteArticle(id) {
      this.$confirm('此操作将永久删除该文章, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        deleteArticle(id).then(response => {
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
    toggleSelection() {
      this.$refs.articlesTable.clearSelection()
    },
    deleteSelection() {
      this.$confirm('此操作将永久这些删除文章, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const ids = this.multipleSelection.map(v => v.id)
        deleteArticle(ids).then(response => {
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
    selection() {
      this.centerDialogVisible = true
      this.category = ''
      this.new_status = ''
      this.user = ''
      if (this.$refs.form !== undefined) {
        this.$refs.form.resetFields()
      }
    },
    editSelection() {
      const ids = this.multipleSelection.map(v => v.id)
      if (this.category.length === 0 && this.new_status.length === 0 && this.user.length === 0) {
        this.$message({
          type: 'error',
          message: '请选择需要进行的操作！'
        })
        return
      }
      batchUpdateArticle(ids, this.category, this.new_status, this.user).then(response => {
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
