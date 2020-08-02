package com.cpdms.service.dinning;

import java.util.Date;
import java.util.List;
import java.util.Arrays;

import com.cpdms.util.DateUtils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.AppraiseMapper;
import com.cpdms.model.dinning.Appraise;
import com.cpdms.model.dinning.AppraiseExample;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;

/**
 * 评价表 AppraiseService
 * @Title: AppraiseService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-30 15:47:57  
 **/
@Service
public class AppraiseService implements BaseService<Appraise, AppraiseExample>{
	@Autowired
	private AppraiseMapper appraiseMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<Appraise> list(Tablepar tablepar,String name){
	        AppraiseExample testExample=new AppraiseExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andFoodIdEqualTo(name);
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<Appraise> list= appraiseMapper.selectByExample(testExample);
	        PageInfo<Appraise> pageInfo = new PageInfo<Appraise>(list);
	        return  pageInfo;
	 }

	 /**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<Appraise> listRelative(Tablepar tablepar,String foodName){
	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<Appraise> list= appraiseMapper.selectRelative(foodName);
	        PageInfo<Appraise> pageInfo = new PageInfo<Appraise>(list);
	        return  pageInfo;
	 }

	 // 判断是否可以评价
	 public Boolean canAppraise(Appraise appraise) {
	     AppraiseExample appraiseExample = new AppraiseExample();
	     if (appraise.getSource().equals("daily")) {
	         appraiseExample.createCriteria().andFoodIdEqualTo(appraise.getFoodId())
                     .andCardNoEqualTo(appraise.getCardNo())
                     .andSourceEqualTo("daily")
                     .andCreateTimeGreaterThan(DateUtils.getStartTime(new Date()))
                     .andCreateTimeLessThan(DateUtils.getEndTime(new Date()));
         } else {
	         appraiseExample.createCriteria().andFoodIdEqualTo(appraise.getFoodId())
                     .andCardNoEqualTo(appraise.getCardNo())
                     .andSourceEqualTo("order")
                     .andOrdIdEqualTo(appraise.getOrdId());
         }
         long count = appraiseMapper.countByExample(appraiseExample);
	     if (count > 0) {
	         return false;
         }

        return true;
     }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			AppraiseExample example=new AppraiseExample();
			example.createCriteria().andIdIn(lista);
			return appraiseMapper.deleteByExample(example);


	}


	@Override
	public Appraise selectByPrimaryKey(String id) {

			return appraiseMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(Appraise record) {
		return appraiseMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(Appraise record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return appraiseMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(Appraise record, AppraiseExample example) {

		return appraiseMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(Appraise record, AppraiseExample example) {

		return appraiseMapper.updateByExample(record, example);
	}

	@Override
	public List<Appraise> selectByExample(AppraiseExample example) {

		return appraiseMapper.selectByExample(example);
	}


	@Override
	public long countByExample(AppraiseExample example) {

		return appraiseMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(AppraiseExample example) {

		return appraiseMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param appraise
	 * @return
	 */
	public int checkNameUnique(Appraise appraise){
		AppraiseExample example=new AppraiseExample();
		example.createCriteria().andFoodIdEqualTo(appraise.getFoodId());
		List<Appraise> list=appraiseMapper.selectByExample(example);
		return list.size();
	}


}
