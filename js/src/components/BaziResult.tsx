import React from 'react';
import '../styles/bazi.css';

interface BaziResultProps {
  resultData: ResultData;
  onBack: () => void;
}

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

const BaziResult: React.FC<BaziResultProps> = ({ resultData, onBack }) => {
  const { userInfo, baziChart, elements, dayMaster, missingElements, weakElements, personalityAnalysis, elementSuggestions, products } = resultData;
  
  return (
    <div className="bazi-result" id="bazi-result" style={{ display: 'block' }}>
      {/* 頂部信息區 */}
      <div className="result-header">
        <h2>命格分析結果</h2>
        <div className="user-info">
          <p><span>性別：</span><span id="result-gender">{userInfo.gender}</span></p>
          <p><span>生辰：</span><span id="result-birth">{userInfo.birthDate}</span></p>
          <p><span>出生地：</span><span id="result-birthplace">{userInfo.birthPlace}</span></p>
          <p><span>分析項目：</span><span id="result-analysis-type">{userInfo.analysisType}</span></p>
        </div>
      </div>

      {/* 核心分析區（兩列布局） */}
      <div className="analysis-grid">
        {/* 左列：八字盤和五行信息 */}
        <div className="analysis-column">
          {/* 八字盤區塊 */}
          <div className="analysis-card bazi-chart">
            <h3>八字盤</h3>
            <div className="chart-container">
              <div className="chart-item">
                <div className="chart-title">年柱</div>
                <div className="chart-content" id="year-pillar">
                  <div className="heavenly">{baziChart.yearPillar.heavenly}</div>
                  <div className="earthly">{baziChart.yearPillar.earthly}</div>
                </div>
              </div>
              <div className="chart-item">
                <div className="chart-title">月柱</div>
                <div className="chart-content" id="month-pillar">
                  <div className="heavenly">{baziChart.monthPillar.heavenly}</div>
                  <div className="earthly">{baziChart.monthPillar.earthly}</div>
                </div>
              </div>
              <div className="chart-item">
                <div className="chart-title">日柱</div>
                <div className="chart-content" id="day-pillar">
                  <div className="heavenly">{baziChart.dayPillar.heavenly}</div>
                  <div className="earthly">{baziChart.dayPillar.earthly}</div>
                </div>
              </div>
              <div className="chart-item">
                <div className="chart-title">時柱</div>
                <div className="chart-content" id="hour-pillar">
                  <div className="heavenly">{baziChart.hourPillar.heavenly}</div>
                  <div className="earthly">{baziChart.hourPillar.earthly}</div>
                </div>
              </div>
            </div>
          </div>
          
          {/* 五行分佈區塊 */}
          <div className="analysis-card five-elements">
            <h3>五行分佈</h3>
            <div className="elements-container">
              <div className="element-item">
                <div className="element-name">金</div>
                <div className="element-value" id="metal-value">{elements.metal}</div>
              </div>
              <div className="element-item">
                <div className="element-name">木</div>
                <div className="element-value" id="wood-value">{elements.wood}</div>
              </div>
              <div className="element-item">
                <div className="element-name">水</div>
                <div className="element-value" id="water-value">{elements.water}</div>
              </div>
              <div className="element-item">
                <div className="element-name">火</div>
                <div className="element-value" id="fire-value">{elements.fire}</div>
              </div>
              <div className="element-item">
                <div className="element-name">土</div>
                <div className="element-value" id="earth-value">{elements.earth}</div>
              </div>
            </div>
          </div>
          
          {/* 五行狀態與日主區塊 */}
          <div className="analysis-card five-elements-status">
            <div className="five-status-grid">
              <div className="five-status-item">
                <h3>日主</h3>
                <p id="day-master-value">{dayMaster.value}</p>
                <p id="day-master-strength">{dayMaster.strength}</p>
              </div>
              <div className="five-status-item">
                <h3>缺失五行</h3>
                <p id="missing-elements">{missingElements}</p>
              </div>
              <div className="five-status-item">
                <h3>偏弱五行</h3>
                <p id="weak-elements">{weakElements}</p>
              </div>
            </div>
          </div>
        </div>
        
        {/* 右列：性格分析和五行補充建議 */}
        <div className="analysis-column" id="extended-analysis">
          {/* 性格分析區塊 */}
          <div className="analysis-card personality-analysis">
            <h3>性格分析</h3>
            <p>{personalityAnalysis}</p>
          </div>
          
          {/* 五行補充建議區塊 */}
          <div className="analysis-card element-suggestions">
            <h3>五行補充建議</h3>
            <p>{elementSuggestions}</p>
          </div>
        </div>
      </div>
      
      {/* 相關商品區塊 */}
      <div className="related-products" id="related-products">
        <h2>推薦商品</h2>
        <p className="recommendation-intro">根據您的八字分析，以下商品可能適合您:</p>
        <div className="products-container" id="products-container">
          {products && products.length > 0 ? (
            products.map((product) => (
              <div key={product.id} className="product-card">
                <img src={product.image} alt={product.name} className="product-image" />
                <div className="product-info">
                  <div className="product-name">{product.name}</div>
                  <div className="product-description">{product.description}</div>
                  <div className="product-price">{product.price}</div>
                  <div className="product-elements">
                    {product.elements.map((element, index) => (
                      <span key={index} className="product-element">{element}</span>
                    ))}
                  </div>
                  <a href={product.url} className="product-button">查看商品</a>
                </div>
              </div>
            ))
          ) : (
            <div className="no-products">暫無相關推薦商品</div>
          )}
        </div>
      </div>
      
      <button className="back-btn" id="back-btn" onClick={onBack}>返回</button>
    </div>
  );
};

export default BaziResult; 