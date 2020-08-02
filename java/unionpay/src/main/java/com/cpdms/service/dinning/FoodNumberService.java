package com.cpdms.service.dinning;

import java.util.Date;
import java.util.List;
import java.text.SimpleDateFormat;
import java.util.Arrays;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.FoodNumberMapper;
import com.cpdms.model.dinning.FoodNumber;
import com.cpdms.model.dinning.FoodNumberExample;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;

/**
 * 菜品期数 FoodNumberService
 * @Title: FoodNumberService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-30 16:31:30  
 **/
@Service
public class FoodNumberService implements BaseService<FoodNumber, FoodNumberExample>{
	@Autowired
	private FoodNumberMapper foodNumberMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<FoodNumber> list(Tablepar tablepar,Integer number){
	        FoodNumberExample testExample=new FoodNumberExample();
	        testExample.setOrderByClause("id ASC");
	        if(number!=null&&!"".equals(number)){
	        	testExample.createCriteria().andNumberEqualTo(number);
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<FoodNumber> list= foodNumberMapper.selectByExample(testExample);
	        PageInfo<FoodNumber> pageInfo = new PageInfo<FoodNumber>(list);
	        return  pageInfo;
	 }

	  /**
	 * 获取所有
	 * @return
	 */
	 public List<FoodNumber> getAll(){
	        FoodNumberExample testExample=new FoodNumberExample();
	        List<FoodNumber> list= foodNumberMapper.selectByExample(testExample);
	        return list;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			FoodNumberExample example=new FoodNumberExample();
			example.createCriteria().andIdIn(lista);
			return foodNumberMapper.deleteByExample(example);


	}


	@Override
	public FoodNumber selectByPrimaryKey(String id) {

			return foodNumberMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(FoodNumber record) {

		return foodNumberMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(FoodNumber record) {
			FoodNumberExample testExample=new FoodNumberExample();
			//校验期数与时间
			if (record.getNumber() > 1) {
				FoodNumber foodNumber= foodNumberMapper.selectByNumber(record.getNumber() - 1);
				System.out.println(foodNumber);
				if (foodNumber == null || "".equals(foodNumber.getId())) {
					return 0;
				}
				long between = record.getBeginTime().getTime() - foodNumber.getEndTime().getTime();
				if (between  != 0) {
					return 0;
				}
			}

			//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return foodNumberMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(FoodNumber record, FoodNumberExample example) {

		return foodNumberMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(FoodNumber record, FoodNumberExample example) {

		return foodNumberMapper.updateByExample(record, example);
	}

	@Override
	public List<FoodNumber> selectByExample(FoodNumberExample example) {

		return foodNumberMapper.selectByExample(example);
	}


	@Override
	public long countByExample(FoodNumberExample example) {

		return foodNumberMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(FoodNumberExample example) {

		return foodNumberMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param foodNumber
	 * @return
	 */
	public int checkNameUnique(FoodNumber foodNumber){
		FoodNumberExample example=new FoodNumberExample();
		example.createCriteria().andNumberEqualTo(foodNumber.getNumber());
		List<FoodNumber> list=foodNumberMapper.selectByExample(example);
		return list.size();
	}

	public FoodNumber getCurrent() {
	    return foodNumberMapper.getByDate(new Date());
    }

	public FoodNumber getByDate(Date date) {
		return foodNumberMapper.getByDate(date);
	}
}
