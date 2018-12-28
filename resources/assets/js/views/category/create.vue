<template>
  <category-detail :is-edit="false"/>
</template>

<script>
import CategoryDetail from './components/CategoryDetail'

export default {
  name: 'CreateForm',
  components: { CategoryDetail }
}
</script>

