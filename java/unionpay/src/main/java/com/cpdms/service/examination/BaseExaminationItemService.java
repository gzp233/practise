package com.cpdms.service.examination;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.examination.BaseExaminationItemMapper;
import com.cpdms.mapper.examination.RefExaminationItemMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.examination.BaseExaminationItem;
import com.cpdms.model.examination.BaseExaminationItemExample;
import com.cpdms.model.examination.RefExaminationItemExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import org.springframework.transaction.annotation.Transactional;

/**
 *  BaseExaminationItemService
 * @Title: BaseExaminationItemService.java 
 * @Package com.cpdms.vote.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-07 10:10:02  
 **/
@Service
public class BaseExaminationItemService implements BaseService<BaseExaminationItem, BaseExaminationItemExample> {
	@Autowired
	private BaseExaminationItemMapper baseExaminationItemMapper;
	@Autowired
    private RefExaminationItemMapper refExaminationItemMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseExaminationItem> list(Tablepar tablepar, String name){
	        BaseExaminationItemExample testExample=new BaseExaminationItemExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andItemNameLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseExaminationItem> list= baseExaminationItemMapper.selectByExample(testExample);
	        PageInfo<BaseExaminationItem> pageInfo = new PageInfo<BaseExaminationItem>(list);
	        return  pageInfo;
	 }

	@Override
    @Transactional
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BaseExaminationItemExample example=new BaseExaminationItemExample();
			example.createCriteria().andIdIn(lista);
			RefExaminationItemExample refExaminationItemExample = new RefExaminationItemExample();
            refExaminationItemExample.createCriteria().andExaminationItemIdIn(lista);
            refExaminationItemMapper.deleteByExample(refExaminationItemExample);
			return baseExaminationItemMapper.deleteByExample(example);


	}

	public List<BaseExaminationItem> getAll() {
	     BaseExaminationItemExample example=new BaseExaminationItemExample();
			return baseExaminationItemMapper.selectByExample(example);
    }


	@Override
	public BaseExaminationItem selectByPrimaryKey(String id) {

			return baseExaminationItemMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BaseExaminationItem record) {
		return baseExaminationItemMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseExaminationItem record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return baseExaminationItemMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseExaminationItem record, BaseExaminationItemExample example) {

		return baseExaminationItemMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseExaminationItem record, BaseExaminationItemExample example) {

		return baseExaminationItemMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseExaminationItem> selectByExample(BaseExaminationItemExample example) {

		return baseExaminationItemMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseExaminationItemExample example) {

		return baseExaminationItemMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseExaminationItemExample example) {

		return baseExaminationItemMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseExaminationItem
	 * @return
	 */
	public int checkNameUnique(BaseExaminationItem baseExaminationItem){
		BaseExaminationItemExample example=new BaseExaminationItemExample();
		example.createCriteria().andItemNameEqualTo(baseExaminationItem.getItemName());
		List<BaseExaminationItem> list=baseExaminationItemMapper.selectByExample(example);
		return list.size();
	}


}
