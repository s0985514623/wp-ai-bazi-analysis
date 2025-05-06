import '@/assets/scss/index.scss'
import { ConfigProvider } from 'antd'
import BaziPage from './pages/BaziPage'

// 中文主题设置
const theme = {
  token: {
    colorPrimary: '#9E1E1E', // 设置红色主题
    borderRadius: 4,
    fontSize: 16,
  },
  components: {
    Form: {
      labelColor: '#9c0e0e',
      colorPrimary: '#a83232',
    },
    Button: {
      colorPrimary: '#a83232',
      algorithm: true,
    },
  },
}

function App() {
  return (
    <ConfigProvider theme={theme}>
      <BaziPage />
    </ConfigProvider>
  )
}

export default App
