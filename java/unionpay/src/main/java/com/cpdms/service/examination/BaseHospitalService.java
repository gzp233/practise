package com.cpdms.service.examination;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.examination.BaseHospitalMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.examination.BaseHospital;
import com.cpdms.model.examination.BaseHospitalExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 * 医院列表 BaseHospitalService
 * @Title: BaseHospitalService.java 
 * @Package com.cpdms.hair.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:52:17  
 **/
@Service
public class BaseHospitalService implements BaseService<BaseHospital, BaseHospitalExample> {
	@Autowired
	private BaseHospitalMapper baseHospitalMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseHospital> list(Tablepar tablepar, String name){
	        BaseHospitalExample testExample=new BaseHospitalExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andHospitalNameLike("%"+name+"%");
	        }
	        testExample.createCriteria().andStatusEqualTo(1);

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseHospital> list= baseHospitalMapper.selectByExample(testExample);
	        PageInfo<BaseHospital> pageInfo = new PageInfo<BaseHospital>(list);
	        return  pageInfo;
	 }

	 public List<BaseHospital> getAll() {
	     BaseHospitalExample testExample=new BaseHospitalExample();
	     testExample.createCriteria().andStatusEqualTo(1);
	     return baseHospitalMapper.selectByExample(testExample);
     }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BaseHospitalExample example=new BaseHospitalExample();
			example.createCriteria().andIdIn(lista);
			BaseHospital baseHospital = new BaseHospital();
			baseHospital.setStatus(0);
			return baseHospitalMapper.updateByExampleSelective(baseHospital, example);


	}


	@Override
	public BaseHospital selectByPrimaryKey(String id) {

			return baseHospitalMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BaseHospital record) {
		return baseHospitalMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseHospital record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return baseHospitalMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseHospital record, BaseHospitalExample example) {

		return baseHospitalMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseHospital record, BaseHospitalExample example) {

		return baseHospitalMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseHospital> selectByExample(BaseHospitalExample example) {

		return baseHospitalMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseHospitalExample example) {

		return baseHospitalMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseHospitalExample example) {

		return baseHospitalMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseHospital
	 * @return
	 */
	public int checkNameUnique(BaseHospital baseHospital){
		BaseHospitalExample example=new BaseHospitalExample();
		example.createCriteria().andHospitalNameEqualTo(baseHospital.getHospitalName());
		List<BaseHospital> list=baseHospitalMapper.selectByExample(example);
		return list.size();
	}


}
