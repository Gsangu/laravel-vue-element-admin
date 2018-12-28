<template>
  <el-select v-model="status" placeholder="文章状态" style="width:100px">
    <el-option
      v-for="item in statusOptions"
      :key="item.value"
      :label="item.label"
      :value="item.value"/>
  </el-select>
</template>

<script>
export default {
  props: ['value'],
  data() {
    return {
      statusOptions: [
        { label: '已发布', value: 1 },
        { label: '草稿', value: 0 }
      ]
    }
  },
  computed: {
    status: {
      get() {
        return this.value
      },
      set(val) {
        this.$emit('input', val)
      }
    }
  }
}
</script>
