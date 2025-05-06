import React, { useState } from 'react';
import BaziForm from '../components/BaziForm';
import BaziResult from '../components/BaziResult';
import '../styles/bazi.css';

interface FormData {
  gender: string;
  year: string;
  month: string;
  day: string;
  hour: string;
  country: string;
  city: string;
  analysis_type: string;
}

// API返回结果接口
type BaziApiResponse = {
  bazi: {
    year: { heavenly: string; earthly: string };
    month: { heavenly: string; earthly: string };
    day: { heavenly: string; earthly: string };
    hour: { heavenly: string; earthly: string };
  };
  fiveElements: {
    metal: number;
    wood: number;
    water: number;
    fire: number;
    earth: number;
  };
  dayMaster: string;
  dayMasterStrength: string;
  missingElements: string;
  weakElements: string;
  personalityAnalysis: string;
  elementSuggestions: string;
  relatedProducts: Array<{
    id: string;
    name: string;
    description: string;
    price: string;
    image: string;
    elements: string[];
    url: string;
  }>;
}

// BaziResult组件所需的数据格式
interface ResultData {
  userInfo: {
    gender: string;
    birthDate: string;
    birthPlace: string;
    analysisType: string;
  };
  baziChart: {
    yearPillar: { heavenly: string; earthly: string };
    monthPillar: { heavenly: string; earthly: string };
    dayPillar: { heavenly: string; earthly: string };
    hourPillar: { heavenly: string; earthly: string };
  };
  elements: {
    metal: number;
    wood: number;
    water: number;
    fire: number;
    earth: number;
  };
  dayMaster: {
    value: string;
    strength: string;
  };
  missingElements: string;
  weakElements: string;
  personalityAnalysis: string;
  elementSuggestions: string;
  products: Array<{
    id: string;
    name: string;
    description: string;
    price: string;
    image: string;
    elements: string[];
    url: string;
  }>;
}

const BaziPage: React.FC = () => {
  const [showResult, setShowResult] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [resultData, setResultData] = useState<ResultData | null>(null);
  const [error, setError] = useState<string | null>(null);
  
  // 性別映射
  const genderMap: Record<string, string> = {
    male: '男',
    female: '女'
  };

  // 分析類型映射
  const analysisTypeMap: Record<string, string> = {
    general: '總體運勢',
    love: '感情姻緣',
    career: '工作事業',
    wealth: '財運',
    health: '健康'
  };

  // 处理表单提交
  const handleFormSubmit = async (formData: FormData) => {
    setIsLoading(true);
    setError(null);
    
    try {
      // 调用WordPress REST API
      const response = await fetch('/wp-json/bft/v1/analyze', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });
      
      if (!response.ok) {
        throw new Error(`API請求失敗: ${response.status}`);
      }
      
      const apiResponse = await response.json() as BaziApiResponse;
      console.log('API Response:', apiResponse);
      
      // 确保API响应中包含所需数据
      if (!apiResponse.bazi || !apiResponse.fiveElements) {
        throw new Error('API返回數據格式不正確');
      }
      
      // 处理API响应数据，转换为结果格式
      const formattedResult: ResultData = {
        userInfo: {
          gender: genderMap[formData.gender] || formData.gender,
          birthDate: `${formData.year}年${formData.month}月${formData.day}日 ${getChineseHour(formData.hour)}`,
          birthPlace: `${formData.country} ${formData.city}`,
          analysisType: analysisTypeMap[formData.analysis_type] || formData.analysis_type
        },
        baziChart: {
          yearPillar: apiResponse.bazi.year,
          monthPillar: apiResponse.bazi.month,
          dayPillar: apiResponse.bazi.day,
          hourPillar: apiResponse.bazi.hour
        },
        elements: {
          metal: apiResponse.fiveElements.metal || 0,
          wood: apiResponse.fiveElements.wood || 0,
          water: apiResponse.fiveElements.water || 0,
          fire: apiResponse.fiveElements.fire || 0,
          earth: apiResponse.fiveElements.earth || 0
        },
        dayMaster: {
          value: apiResponse.dayMaster || '',
          strength: apiResponse.dayMasterStrength || ''
        },
        missingElements: apiResponse.missingElements || '',
        weakElements: apiResponse.weakElements || '',
        personalityAnalysis: apiResponse.personalityAnalysis || '',
        elementSuggestions: apiResponse.elementSuggestions || '',
        products: Array.isArray(apiResponse.relatedProducts) ? 
          apiResponse.relatedProducts.map((product: any) => ({
            id: product.id || '',
            name: product.name || '',
            description: product.description || '',
            price: typeof product.price === 'number' ? `NT$${product.price}` : product.price || '',
            image: product.image || 'https://via.placeholder.com/300x200?text=商品圖片',
            elements: Array.isArray(product.elements) ? product.elements : [],
            url: product.url || '#'
          })) : []
      };
      
      setResultData(formattedResult);
      setShowResult(true);
    } catch (err) {
      console.error('API調用錯誤:', err);
      setError(err instanceof Error ? err.message : '未知錯誤');
    } finally {
      setIsLoading(false);
    }
  };

  // 返回表单
  const handleBack = () => {
    setShowResult(false);
  };

  // 将时辰编号转换为中文时辰名称
  function getChineseHour(hour: string): string {
    const hoursMap: Record<string, string> = {
      '0': '子時 (23:00-1:00)',
      '1': '丑時 (1:00-3:00)',
      '2': '寅時 (3:00-5:00)',
      '3': '卯時 (5:00-7:00)',
      '4': '辰時 (7:00-9:00)',
      '5': '巳時 (9:00-11:00)',
      '6': '午時 (11:00-13:00)',
      '7': '未時 (13:00-15:00)',
      '8': '申時 (15:00-17:00)',
      '9': '酉時 (17:00-19:00)',
      '10': '戌時 (19:00-21:00)',
      '11': '亥時 (21:00-23:00)',
      'unknown': '時辰不詳'
    };
    
    return hoursMap[hour] || '時辰不詳';
  }

  return (
    <div className="container">
      {/* 左側表單區域 */}
      <div className="form-section" id="form-section" style={{ display: showResult ? 'none' : 'flex' }}>
        <BaziForm onSubmit={handleFormSubmit} />
        {error && (
          <div className="error-message">
            <p>錯誤: {error}</p>
            <p>請稍後再試或聯繫管理員</p>
          </div>
        )}
      </div>

      {/* 右側視頻/結果區域 */}
      <div className="result-section" id="result-section" style={{ display: showResult ? 'none' : 'flex' }}>
        <div className="video-container" id="video-container">
          <video autoPlay muted loop id="background-video">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-ink-swirling-in-water-89p-large.mp4" type="video/mp4" />
          </video>
          <div className="video-overlay">
            <h2>八字命格</h2>
            <p>中國傳統命理分析，揭示人生吉凶禍福</p>
          </div>
        </div>
      </div>

      {/* 加載動畫 */}
      <div className="loading-overlay" id="loading-overlay" style={{ display: isLoading ? 'flex' : 'none', opacity: isLoading ? 1 : 0 }}>
        <div className="loading-content">
          <div className="loading-symbol">
            <div className="yin-yang"></div>
          </div>
          <p>命運之輪轉動中...</p>
        </div>
      </div>
      
      {/* 結果區域 */}
      {showResult && resultData && (
        <BaziResult resultData={resultData} onBack={handleBack} />
      )}
    </div>
  );
};

export default BaziPage; 